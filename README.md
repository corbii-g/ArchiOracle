# ArchiOracle

## 1. Project Overview

**ArchiOracle** is an interactive web application inspired by the classic Magic 8-Ball.  

Users type a vague question or statement, then “shake” a digital 8-ball. The ball returns a random **vague, mysterious, or funny phrase** stored in a MySQL database.

The project is designed to be:

- Fun and engaging for a school open day
- Responsive (works well on phones and PCs)
- Built with the required technologies: **PHP**, **Python**, and **MySQL**

---

## 2. Core Concept

1. User opens the website on phone or computer.
2. User can contribute to the project by writing a vague answer, but can skip this option.
3. User types a vague question or statement (e.g. “Will I pass the test?”, “Should I eat pizza tonight?”).
4. User shakes the Fortnite-style 8-ball.
5. After a short shake animation, a random vague phrase appears inside the ball.
6. The phrase comes from a MySQL database (never hardcoded in the frontend).

The answers should feel deliberately vague, mysterious, or playfully unhelpful — this is the “vague” twist of the project.

---

## 3. Technology Stack

| Layer              | Technology          | Role |
|--------------------|---------------------|------|
| Frontend           | HTML, CSS, JavaScript | Interface, shake animation, form |
| Backend (web)      | PHP                 | Serve pages, handle form, query database, return answer |
| Database           | MySQL               | Store all vague phrases |
| Supporting script  | Python              | Populate / manage the database (seed script, optional admin tools) |

**Why both PHP and Python?**
- PHP is used for the live web application (easy to host on school servers / shared hosting).
- Python is used for a clean command-line tool that inserts, updates, or lists phrases in the database. This shows you can work with both languages and keeps data management separate from the web code.

---

## 4. User Flow

```
[ Landing Page ]
       │
       ▼
User types a vague question / statement
       │
       ▼
User clicks "Shake the Ball" (or shakes phone)
       │
       ▼
Ball plays shake animation (1–2 seconds)
       │
       ▼
PHP backend randomly selects one phrase from MySQL
       │
       ▼
Vague answer appears inside the 8-ball
       │
       ▼
User can ask another question
```

---

## 5. Database Design (MySQL)

### Table: `phrases`

| Column       | Type         | Description                          |
|--------------|--------------|--------------------------------------|
| `id`         | INT (PK, AI) | Unique identifier                    |
| `phrase`     | VARCHAR(255) | The vague answer text                |
| `category`   | VARCHAR(50)  | Optional (e.g. positive, negative, neutral, funny) |
| `created_at` | TIMESTAMP    | When the phrase was added            |

### Example phrases (you can expand these)

**Neutral / Vague**
- Reply hazy, try again later
- The answer is floating in the storm
- Better not tell you now
- Ask again after the next storm circle
- Signs point to... maybe

**Positive-ish**
- It is certain... probably
- Without a doubt (in this reality)
- Yes – but only if you build first
- Outlook good, for now

**Negative-ish**
- Don’t count on it
- My sources say no
- Very doubtful
- The storm says otherwise


---
