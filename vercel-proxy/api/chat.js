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

  const { skinType, skinDescription, answers, goal, routine, products } = req.body;
  const siteUrl = req.headers.origin || "https://institutcorpsacoeur.fr";

  if (!skinType || !answers || !products) {
    res.writeHead(400, cors);
    return res.end(JSON.stringify({ error: "Missing required fields" }));
  }

  const client = new Anthropic();

  const systemPrompt = `Tu es la conseillère beauté de l'Institut Corps & Cœur, un institut de beauté et bien-être situé à Mézidon Canon (Calvados).

Tu viens d'analyser les réponses d'un diagnostic de peau. Tu dois fournir une analyse personnalisée et recommander les produits les plus adaptés.

Ton style :
- Français courant, tutoiement, ton chaleureux et bienveillant
- Comme une amie experte qui donne des conseils sincères
- Concise mais précise (max 350 mots)
- Pas de formules de politesse superflues, va droit au but

Structure ta réponse ainsi :

### Ton type de peau
Un court paragraphe (2-3 phrases) qui explique le type de peau détecté de façon concrète et rassurante. Pas de jargon inutile. Intègre l'objectif et la routine de la personne si disponibles.

### Ta routine idéale
Recommande 2 à 4 produits en expliquant POURQUOI chacun convient à ce type de peau et QUAND l'utiliser (matin/soir). Organise par étape (nettoyage, soin, protection).
Pour chaque produit : [**Nom du produit**](url) — prix€

### Notre coup de cœur
Si un coffret ou un produit star correspond particulièrement, mets-le en avant.

### Mon conseil
Un conseil pratique personnalisé (geste, habitude, fréquence) lié au type de peau.

Règles strictes :
- Ne recommande QUE des produits présents dans la liste fournie
- Utilise les URLs telles quelles (ne les modifie pas)
- Si tu ne trouves pas de produit adapté pour une étape, ne l'invente pas
- Privilégie les produits dont la description mentionne des bénéfices liés au type de peau`;

  // Construire le contexte utilisateur
  let userContext = `Type de peau détecté : ${skinType}`;
  if (skinDescription) {
    userContext += `\nDescription : ${skinDescription}`;
  }
  if (goal) {
    userContext += `\nObjectif principal : ${goal}`;
  }
  if (routine) {
    userContext += `\nRoutine actuelle : ${routine}`;
  }

  const userMessage = `${userContext}

Réponses au diagnostic :
${answers.map((a) => `- ${a.question}: ${a.answer}`).join("\n")}

Produits disponibles :
${products.map((p) => `- ${p.name} (${p.price}€) [${p.category}] URL: ${siteUrl}${p.url}${p.description ? ` — ${p.description}` : ""}`).join("\n")}`;

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
