const { MongoClient } = require('mongodb');

const uri = process.env.MONGO_URI; // mongodb+srv://usuario:password@cluster.mongodb.net/AIContentCreator
let client;

async function conectar() {
  if (!client) {
    client = new MongoClient(uri, { useNewUrlParser: true, useUnifiedTopology: true });
    await client.connect();
  }
  return client.db('AIContentCreator'); // nombre exacto de tu base de datos
}

module.exports = { conectar };
