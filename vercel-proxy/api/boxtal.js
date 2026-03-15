/**
 * Proxy pour l'API Boxtal Connect (appel côté client).
 * OVH mutualisé bloque les connexions HTTPS sortantes côté PHP.
 * Le navigateur du client appelle ce proxy après confirmation de paiement.
 *
 * Sécurité : Laravel signe le payload avec HMAC-SHA256.
 * Les credentials Boxtal sont stockées dans les env vars Vercel (jamais exposées).
 *
 * Env vars requises sur Vercel :
 *   - BOXTAL_ACCESS_KEY
 *   - BOXTAL_SECRET_KEY
 *   - BOXTAL_PROXY_SECRET  (clé HMAC partagée avec Laravel)
 */

import { createHmac } from "crypto";

const PROXY_SECRET = process.env.BOXTAL_PROXY_SECRET;
const BOXTAL_ACCESS_KEY = process.env.BOXTAL_ACCESS_KEY;
const BOXTAL_SECRET_KEY = process.env.BOXTAL_SECRET_KEY;
const BOXTAL_BASE_URL = "https://api.boxtal.com";

const ALLOWED_ORIGINS = [
  "https://institutcorpsacoeur.fr",
  "http://localhost:8000",
  "http://127.0.0.1:8000",
];

function getCorsHeaders(origin) {
  const allowed = ALLOWED_ORIGINS.includes(origin) ? origin : ALLOWED_ORIGINS[0];
  return {
    "Access-Control-Allow-Origin": allowed,
    "Access-Control-Allow-Methods": "POST, OPTIONS",
    "Access-Control-Allow-Headers": "Content-Type",
  };
}

function verifyHmac(payload, signature) {
  if (!PROXY_SECRET || !signature) return false;
  const expected = createHmac("sha256", PROXY_SECRET)
    .update(JSON.stringify(payload))
    .digest("hex");
  return expected === signature;
}

export default async function handler(req, res) {
  const origin = req.headers.origin || "";
  const cors = getCorsHeaders(origin);

  if (req.method === "OPTIONS") {
    res.writeHead(204, cors);
    return res.end();
  }

  if (req.method !== "POST") {
    return res.status(405).json({ error: "Method not allowed" });
  }

  const { signature, payload } = req.body;

  // Vérifier la signature HMAC
  if (!verifyHmac(payload, signature)) {
    res.writeHead(403, cors);
    return res.end(JSON.stringify({ error: "Invalid signature" }));
  }

  if (!BOXTAL_ACCESS_KEY || !BOXTAL_SECRET_KEY) {
    res.writeHead(500, cors);
    return res.end(JSON.stringify({ error: "Boxtal credentials not configured" }));
  }

  const auth = Buffer.from(`${BOXTAL_ACCESS_KEY}:${BOXTAL_SECRET_KEY}`).toString("base64");

  try {
    const response = await fetch(`${BOXTAL_BASE_URL}/v2/orders`, {
      method: "POST",
      headers: {
        Authorization: auth,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(payload),
    });

    const data = await response.text();

    Object.entries(cors).forEach(([k, v]) => res.setHeader(k, v));
    res.status(response.status);
    res.setHeader("Content-Type", "application/json");
    res.end(data);
  } catch (error) {
    console.error("Boxtal proxy error:", error?.message);
    Object.entries(cors).forEach(([k, v]) => res.setHeader(k, v));
    res.status(502).json({ error: error?.message || "Proxy error" });
  }
}
