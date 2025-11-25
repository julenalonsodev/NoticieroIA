const { MongoClient } = require('mongodb');

const uri = process.env.MONGODB_URI || process.env.MONGO_URI; // Support both variable names
let client;

async function conectar() {
  if (!client) {
    client = new MongoClient(uri, { useNewUrlParser: true, useUnifiedTopology: true });
    await client.connect();
  }
  return client.db('AIContentCreator'); // nombre exacto de tu base de datos
}

module.exports = { conectar };
