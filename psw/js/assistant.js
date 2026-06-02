// assistant.js - AI Chatbot Frontend Interactivity

document.addEventListener('DOMContentLoaded', () => {
  const trigger = document.getElementById('chatbotTrigger');
  const windowEl = document.getElementById('chatbotWindow');
  const closeBtn = document.getElementById('chatbotClose');
  const inputEl = document.getElementById('chatbotInput');
  const sendBtn = document.getElementById('chatbotSend');
  const bodyEl = document.getElementById('chatbotBody');

  let chatHistory = [];

  // Toggle Window
  const toggleChat = () => {
    const isOpen = windowEl.classList.toggle('open');
    trigger.classList.toggle('active', isOpen);
    if (isOpen) {
      inputEl.focus();
      // Scroll to bottom on open
      bodyEl.scrollTop = bodyEl.scrollHeight;
    }
  };

  trigger.addEventListener('click', toggleChat);
  closeBtn.addEventListener('click', toggleChat);

  // Formatting helper for bold text (**text**)
  const formatText = (text) => {
    let formatted = text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
    
    // Bold: **text**
    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    // Italic: *text*
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    // Line breaks
    formatted = formatted.replace(/\n/g, '<br>');
    
    return formatted;
  };

  // Add Message bubble
  const appendMessage = (role, text) => {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('chat-msg', role);

    const avatar = document.createElement('div');
    avatar.classList.add('chatbot-avatar');
    avatar.innerText = role === 'bot' ? '🤖' : '👤';

    const bubble = document.createElement('div');
    bubble.classList.add('msg-bubble');
    bubble.innerHTML = formatText(text);

    if (role === 'bot') {
      msgDiv.appendChild(avatar);
    }
    msgDiv.appendChild(bubble);
    bodyEl.appendChild(msgDiv);

    // Auto scroll to bottom
    bodyEl.scrollTop = bodyEl.scrollHeight;
  };

  // Show/Hide Typing Indicator
  let typingIndicator = null;
  const showTyping = () => {
    if (typingIndicator) return;
    
    typingIndicator = document.createElement('div');
    typingIndicator.classList.add('typing-indicator');
    typingIndicator.innerHTML = `
      <span class="typing-dot"></span>
      <span class="typing-dot"></span>
      <span class="typing-dot"></span>
    `;
    bodyEl.appendChild(typingIndicator);
    bodyEl.scrollTop = bodyEl.scrollHeight;
  };

  const hideTyping = () => {
    if (typingIndicator) {
      typingIndicator.remove();
      typingIndicator = null;
    }
  };

  // Send Message Logic
  const sendMessage = async (customText = '') => {
    const text = (customText || inputEl.value).trim();
    if (!text) return;

    if (!customText) {
      inputEl.value = '';
    }

    // Add user message to UI and history
    appendMessage('user', text);
    
    // Show typing loader
    showTyping();

    try {
      const response = await fetch('php/assistant_handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          message: text,
          history: chatHistory
        })
      });

      const data = await response.json();
      hideTyping();

      const reply = data.reply || 'Maaf, terjadi kendala saat memproses jawaban.';
      appendMessage('bot', reply);

      // Save conversation in session history
      chatHistory.push({ role: 'user', text: text });
      chatHistory.push({ role: 'model', text: reply });

    } catch (error) {
      hideTyping();
      appendMessage('bot', 'Maaf, terjadi kesalahan koneksi. Silakan hubungi kami melalui WhatsApp.');
      console.error('Chat error:', error);
    }
  };

  // Event Listeners
  sendBtn.addEventListener('click', () => sendMessage());
  inputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      sendMessage();
    }
  });

  // Bind Quick Option buttons
  window.triggerQuickOption = (el) => {
    const text = el.innerText;
    sendMessage(text);
  };
});
