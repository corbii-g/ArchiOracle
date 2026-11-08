import mysql.connector
from mysql.connector import Error

# ---- Connection settings  ----
config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',          
    'database': 'archioracle'  
}

# ---- List of vague phrases ----
phrases = [
    ("Le probabilità sono difficili da interpretare", "neutral"),
    ("Il futuro non è ancora scritto", "neutral"),
    ("Forse è meglio aspettare ancora un po'", "neutral"),
    ("La risposta arriverà al momento giusto", "neutral"),
    ("Non tutto è come sembra", "neutral"),
    ("Gli indizi sono contrastanti", "neutral"),
    ("È possibile, ma non garantito", "neutral"),
    ("Le circostanze sembrano favorevoli", "positive"),
    ("Puoi fidarti del tuo istinto", "positive"),
    ("Tutto lascia pensare di sì", "positive"),
    ("Le stelle sembrano essere dalla tua parte", "positive"),
    ("La risposta è più vicina di quanto pensi", "positive"),
    ("Non sembra il momento giusto", "negative"),
    ("Le possibilità non sono molte", "negative"),
    ("Meglio non farsi troppe illusioni", "negative"),
    ("La risposta sembra essere no", "negative"),
    ("Non aspettarti grandi sorprese", "negative"),
    ("Il destino ha altri programmi", "negative"),
    ("Anche il silenzio è una risposta", "funny"),
    ("Chiedimelo quando avrò le idee più chiare", "funny"),
]

def seed():
    try:
        connection = mysql.connector.connect(**config)
        cursor = connection.cursor()

        # Optional: clear old data
        # cursor.execute("TRUNCATE TABLE phrases")

        insert_query = "INSERT INTO phrases (phrase, category) VALUES (%s, %s)"
        cursor.executemany(insert_query, phrases)

        connection.commit()
        print(f"Successfully inserted {cursor.rowcount} phrases.")

    except Error as e:
        print(f"Error: {e}")
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection closed.")

if __name__ == "__main__":
    seed()