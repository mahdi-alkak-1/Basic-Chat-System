📩 Chat System — Full-Stack Project

A lightweight real-time messaging system built with PHP (OOP + MVC), MySQL, HTML/CSS/JS, and Axios.
Includes message status tracking (sent / delivered / read) and an AI Catch-Up Summary using OpenAI.

🚀 Features
💬 1. One-to-One Conversations

Start chat with any registered user.

Auto-detects existing conversations — no duplicates.

Clean UI similar to Discord.

📨 2. Messaging with Delivery Logic

Each message supports:

Sent ✔

Delivered ✔✔ (blue)

Read ✔✔ (green)

The logic is handled with:

markDelivered() → When conversation opens

markRead() → After a short delay inside the chat

🤖 3. AI Catch-Up Summary

When a user enters another user's email in Start Chat:

If a conversation exists AND there are 3+ unread messages,

The system generates a catch-up summary using OpenAI GPT-4o-mini.

This allows fast recap of missed messages.

🔐 4. Authentication

Secure login using tokens stored in localStorage.

APIs require X-Auth-Token.

🗄️ 5. Clean Backend Architecture

MVC-style folder structure

Service classes: ConversationService, MessageService, AiService

Controllers: Auth, Conversation, Message, Ai
