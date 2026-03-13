import Anthropic from "@anthropic-ai/sdk";

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

export default async function handler(req, res) {
  const origin = req.headers.origin || "";
  const cors = getCorsHeaders(origin);

  if (req.method === "OPTIONS") {
    res.writeHead(204, cors);
    return res.end();
  }

  if (req.method !== "POST") {
    res.writeHead(405, cors);
    return res.end(JSON.stringify({ error: "Method not allowed" }));
  }

  const { skinType, answers, products } = req.body;

  if (!skinType || !answers || !products) {
    res.writeHead(400, cors);
    return res.end(JSON.stringify({ error: "Missing required fields" }));
  }

  const client = new Anthropic();

  const systemPrompt = `Tu es la conseillère beauté virtuelle de l'Institut Corps & Cœur, un institut de beauté et bien-être situé à Mézidon Canon, près de Caen.

Tu viens d'analyser les réponses d'un quiz diagnostic de peau. Tu dois recommander les produits les plus adaptés au type de peau détecté.

Règles :
- Réponds en français, ton chaleureux et professionnel (tutoiement)
- Commence par expliquer brièvement le type de peau détecté et ses caractéristiques
- Recommande 2 à 4 produits individuels ET le coffret le plus adapté s'il existe
- Pour chaque produit, explique POURQUOI il convient à ce type de peau
- Utilise le format Markdown pour structurer ta réponse
- Pour chaque produit recommandé, mets le nom en gras et indique le prix
- Ne recommande QUE des produits présents dans la liste fournie
- Si un coffret correspond au type de peau, mets-le en avant comme meilleure option
- Termine par un conseil personnalisé de routine
- Sois concise (max 300 mots)`;

  const userMessage = `Type de peau détecté : ${skinType}

Réponses au quiz :
${answers.map((a) => `- ${a.question}: ${a.answer}`).join("\n")}

Produits disponibles (catégorie Produits Visage) :
${products.map((p) => `- ${p.name} (${p.price}€) [${p.category}] : ${p.description || "Pas de description"}`).join("\n")}`;

  try {
    res.writeHead(200, {
      ...cors,
      "Content-Type": "text/event-stream",
      "Cache-Control": "no-cache",
      Connection: "keep-alive",
    });

    const stream = await client.messages.stream({
      model: "claude-haiku-4-5-20251001",
      max_tokens: 1024,
      system: systemPrompt,
      messages: [{ role: "user", content: userMessage }],
    });

    for await (const event of stream) {
      if (
        event.type === "content_block_delta" &&
        event.delta.type === "text_delta"
      ) {
        res.write(`data: ${JSON.stringify({ text: event.delta.text })}\n\n`);
      }
    }

    res.write("data: [DONE]\n\n");
    res.end();
  } catch (error) {
    console.error("Anthropic API error:", error?.message, error?.status, JSON.stringify(error?.error || {}));
    const errMsg = error?.message || "Erreur lors de la génération";
    if (!res.headersSent) {
      res.writeHead(500, cors);
      res.end(JSON.stringify({ error: errMsg }));
    } else {
      res.write(`data: ${JSON.stringify({ error: errMsg })}\n\n`);
      res.end();
    }
  }
}
