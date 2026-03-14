/**
 * Proxy pour l'API Boxtal Connect.
 * OVH mutualisé bloque les connexions sortantes vers api.boxtal.com.
 * Cette fonction Vercel relaie les requêtes depuis le serveur Laravel.
 *
 * Headers attendus du client Laravel :
 *   - Authorization: Basic <base64(access_key:secret_key)>
 *   - X-Proxy-Secret: <BOXTAL_PROXY_SECRET> (partagé entre Laravel et Vercel)
 */

const PROXY_SECRET = process.env.BOXTAL_PROXY_SECRET;
const BOXTAL_BASE_URL = "https://api.boxtal.com";

export default async function handler(req, res) {
  // Seul POST est utilisé pour pushOrder
  if (req.method !== "POST") {
    return res.status(405).json({ error: "Method not allowed" });
  }

  // Vérifier le secret partagé pour éviter les abus
  const proxySecret = req.headers["x-proxy-secret"];
  if (!PROXY_SECRET || proxySecret !== PROXY_SECRET) {
    return res.status(403).json({ error: "Forbidden" });
  }

  const authorization = req.headers["authorization"];
  if (!authorization) {
    return res.status(400).json({ error: "Missing Authorization header" });
  }

  try {
    const response = await fetch(`${BOXTAL_BASE_URL}/v2/orders`, {
      method: "POST",
      headers: {
        Authorization: authorization,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(req.body),
    });

    const data = await response.text();

    res.status(response.status);
    res.setHeader("Content-Type", "application/json");
    res.end(data);
  } catch (error) {
    console.error("Boxtal proxy error:", error?.message);
    res.status(502).json({ error: error?.message || "Proxy error" });
  }
}
