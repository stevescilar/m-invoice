{{--
Invoxa Chatbot Widget
Include in layouts: app.blade.php and welcome.blade.php
Just before </body>:  @include('partials.chatbot')
--}}

<style>
/* ── Bubble ── */
#ivx-bubble {
    position: fixed; bottom: 24px; right: 24px; z-index: 9998;
    width: 56px; height: 56px; border-radius: 50%;
    background: #16a34a;
    box-shadow: 0 4px 20px rgba(22,163,74,0.45);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    border: none; outline: none;
}
#ivx-bubble:hover { transform: scale(1.1); background: #15803d; }
#ivx-bubble.open { transform: scale(0.9); background: #111827; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
#ivx-bubble svg { width: 24px; height: 24px; transition: all 0.2s; }
.ivx-pulse {
    position: absolute; width: 56px; height: 56px; border-radius: 50%;
    border: 2px solid rgba(22,163,74,0.4);
    animation: ivx-ripple 2.5s infinite;
}
@keyframes ivx-ripple {
    0% { transform: scale(1); opacity: 0.7; }
    100% { transform: scale(1.6); opacity: 0; }
}

/* ── Chat window ── */
#ivx-chat {
    position: fixed; bottom: 92px; right: 24px; z-index: 9997;
    width: 360px; max-width: calc(100vw - 32px);
    height: 520px; max-height: calc(100vh - 120px);
    background: #0d1410;
    border: 1px solid rgba(22,163,74,0.2);
    border-radius: 20px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
    display: flex; flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    transform: scale(0.85) translateY(20px);
    opacity: 0;
    pointer-events: none;
}
#ivx-chat.open {
    transform: scale(1) translateY(0);
    opacity: 1;
    pointer-events: all;
}

/* ── Header ── */
.ivx-header {
    background: linear-gradient(135deg, #0f1f14, #0d1a10);
    border-bottom: 1px solid rgba(22,163,74,0.15);
    padding: 16px 18px;
    display: flex; align-items: center; gap: 12px;
    flex-shrink: 0;
}
.ivx-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.2);
}
.ivx-avatar svg { width: 20px; height: 20px; color: #fff; }
.ivx-header-info { flex: 1; min-width: 0; }
.ivx-name { font-size: 14px; font-weight: 700; color: #fff; letter-spacing: -0.2px; }
.ivx-status {
    font-size: 11px; color: #4ade80;
    display: flex; align-items: center; gap: 4px; margin-top: 1px;
}
.ivx-status::before {
    content: ''; width: 6px; height: 6px; background: #4ade80;
    border-radius: 50%; animation: ivx-blink 2s infinite;
}
@keyframes ivx-blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
.ivx-close {
    width: 28px; height: 28px; border-radius: 8px;
    background: rgba(255,255,255,0.06); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.5); transition: all 0.15s;
}
.ivx-close:hover { background: rgba(255,255,255,0.1); color: #fff; }

/* ── Messages ── */
.ivx-messages {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 12px;
    scroll-behavior: smooth;
}
.ivx-messages::-webkit-scrollbar { width: 3px; }
.ivx-messages::-webkit-scrollbar-track { background: transparent; }
.ivx-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

.ivx-msg { display: flex; gap: 8px; animation: ivx-msgIn 0.25s ease; }
@keyframes ivx-msgIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.ivx-msg.user { flex-direction: row-reverse; }

.ivx-msg-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800;
}
.ivx-msg-avatar.bot { background: rgba(22,163,74,0.15); color: #4ade80; }
.ivx-msg-avatar.user-av { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); }

.ivx-bubble-msg {
    max-width: 80%; padding: 10px 14px; border-radius: 14px;
    font-size: 13.5px; line-height: 1.55; word-break: break-word;
}
.ivx-msg.bot .ivx-bubble-msg {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.85);
    border-bottom-left-radius: 4px;
}
.ivx-msg.user .ivx-bubble-msg {
    background: #16a34a;
    color: #fff;
    border-bottom-right-radius: 4px;
}

/* Typing indicator */
.ivx-typing { display: flex; align-items: center; gap: 4px; padding: 10px 14px; }
.ivx-typing span {
    width: 6px; height: 6px; border-radius: 50%;
    background: rgba(255,255,255,0.3);
    animation: ivx-bounce 1.4s infinite;
}
.ivx-typing span:nth-child(2) { animation-delay: 0.2s; }
.ivx-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes ivx-bounce {
    0%,60%,100% { transform: translateY(0); }
    30% { transform: translateY(-6px); background: #4ade80; }
}

/* ── Quick prompts ── */
.ivx-quick {
    padding: 0 16px 12px;
    display: flex; gap: 6px; flex-wrap: wrap;
}
.ivx-quick button {
    font-size: 11.5px; padding: 5px 11px; border-radius: 20px;
    background: rgba(22,163,74,0.1); border: 1px solid rgba(22,163,74,0.2);
    color: #4ade80; cursor: pointer; transition: all 0.15s; white-space: nowrap;
}
.ivx-quick button:hover { background: rgba(22,163,74,0.2); }

/* ── Input ── */
.ivx-input-row {
    padding: 12px 14px;
    border-top: 1px solid rgba(255,255,255,0.06);
    display: flex; gap: 8px; align-items: flex-end;
    background: rgba(255,255,255,0.02);
    flex-shrink: 0;
}
#ivx-input {
    flex: 1; background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 12px; padding: 10px 13px;
    color: #fff; font-size: 13.5px; resize: none;
    outline: none; transition: border-color 0.2s;
    font-family: inherit; line-height: 1.4;
    max-height: 100px; min-height: 40px;
}
#ivx-input::placeholder { color: rgba(255,255,255,0.25); }
#ivx-input:focus { border-color: rgba(22,163,74,0.4); }
#ivx-send {
    width: 38px; height: 38px; border-radius: 10px;
    background: #16a34a; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s; flex-shrink: 0;
}
#ivx-send:hover { background: #22c55e; transform: scale(1.05); }
#ivx-send:disabled { background: rgba(255,255,255,0.1); cursor: not-allowed; transform: none; }
#ivx-send svg { width: 16px; height: 16px; color: #fff; }
</style>

{{-- Pulse ring --}}
<div class="ivx-pulse" id="ivx-pulse-ring" style="position:fixed;bottom:24px;right:24px;z-index:9997;pointer-events:none;"></div>

{{-- Bubble button --}}
<button id="ivx-bubble" aria-label="Open Invoxa Assistant" onclick="ivxToggle()">
<svg id="ivx-icon-chat" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
</svg>
<svg id="ivx-icon-close" style="display:none" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
</svg>
</button>

{{-- Chat window --}}
<div id="ivx-chat">
{{-- Header --}}
<div class="ivx-header">
    <div class="ivx-avatar">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .23 2.668-1.168 2.668H4.968c-1.398 0-2.168-1.668-1.168-2.668L5 14.5"/>
        </svg>
    </div>
    <div class="ivx-header-info">
        <div class="ivx-name">Invoxa Assistant</div>
        <div class="ivx-status">Online — ready to help</div>
    </div>
    <button class="ivx-close" onclick="ivxToggle()" aria-label="Close">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- Messages --}}
<div class="ivx-messages" id="ivx-messages"></div>

{{-- Quick prompts --}}
<div class="ivx-quick" id="ivx-quick">
    @auth
    <button onclick="ivxQuick('What is my total revenue this month?')">📊 Revenue</button>
    <button onclick="ivxQuick('Do I have any overdue invoices?')">⚠️ Overdue</button>
    <button onclick="ivxQuick('How do I send an invoice?')">📤 Send invoice</button>
    <button onclick="ivxQuick('How do I set up M-Pesa on my invoices?')">📱 M-Pesa setup</button>
    @else
    <button onclick="ivxQuick('What does Invoxa do?')">✨ What is Invoxa?</button>
    <button onclick="ivxQuick('How much does Invoxa cost?')">💰 Pricing</button>
    <button onclick="ivxQuick('Does Invoxa support M-Pesa?')">📱 M-Pesa</button>
    <button onclick="ivxQuick('How do I get started?')">🚀 Get started</button>
    @endauth
</div>

{{-- Input --}}
<div class="ivx-input-row">
    <textarea id="ivx-input" placeholder="Ask me anything…" rows="1"
        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();ivxSend();}"
        oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px'"></textarea>
    <button id="ivx-send" onclick="ivxSend()" aria-label="Send">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
        </svg>
    </button>
</div>
</div>

<script>
(function() {
let chatOpen = false;
let history  = [];
let isLoading = false;

// Initial greeting
window.addEventListener('DOMContentLoaded', () => {
    @auth
    const name = "{{ auth()->user()->name ?? 'there' }}".split(' ')[0];
    ivxAddMsg('bot', `Hey ${name}! 👋 I'm your Invoxa assistant. I can see your business data and help with insights, invoices, or any questions. What do you need?`);
    @else
    ivxAddMsg('bot', "Hi! 👋 I'm the Invoxa assistant. I can tell you about features, pricing, M-Pesa setup, and more. How can I help?");
    @endauth
});

window.ivxToggle = function() {
    chatOpen = !chatOpen;
    const chat   = document.getElementById('ivx-chat');
    const bubble = document.getElementById('ivx-bubble');
    const iconChat  = document.getElementById('ivx-icon-chat');
    const iconClose = document.getElementById('ivx-icon-close');
    const pulse  = document.getElementById('ivx-pulse-ring');

    chat.classList.toggle('open', chatOpen);
    bubble.classList.toggle('open', chatOpen);
    iconChat.style.display  = chatOpen ? 'none' : 'block';
    iconClose.style.display = chatOpen ? 'block' : 'none';
    pulse.style.display     = chatOpen ? 'none' : 'block';

    if (chatOpen) {
        setTimeout(() => document.getElementById('ivx-input')?.focus(), 300);
    }
};

window.ivxQuick = function(text) {
    document.getElementById('ivx-input').value = text;
    // Hide quick prompts after first use
    document.getElementById('ivx-quick').style.display = 'none';
    ivxSend();
};

window.ivxSend = async function() {
    if (isLoading) return;
    const input = document.getElementById('ivx-input');
    const msg   = input.value.trim();
    if (!msg) return;

    // Hide quick prompts once user chats
    document.getElementById('ivx-quick').style.display = 'none';

    input.value = '';
    input.style.height = 'auto';
    ivxAddMsg('user', msg);

    isLoading = true;
    document.getElementById('ivx-send').disabled = true;
    const typingId = ivxShowTyping();

    try {
        const res = await fetch('{{ route("chatbot.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: msg, history: history.slice(-10) }),
        });

        ivxRemoveTyping(typingId);
        const data = await res.json();

        if (data.error) {
            ivxAddMsg('bot', '⚠️ ' + data.error);
        } else {
            ivxAddMsg('bot', data.reply);
            history.push({ role: 'user', content: msg });
            history.push({ role: 'assistant', content: data.reply });
            if (history.length > 20) history = history.slice(-20);
        }
    } catch(e) {
        ivxRemoveTyping(typingId);
        ivxAddMsg('bot', '⚠️ Network error. Please check your connection and try again.');
    }

    isLoading = false;
    document.getElementById('ivx-send').disabled = false;
    input.focus();
};

function ivxAddMsg(role, text) {
    const container = document.getElementById('ivx-messages');
    const wrap = document.createElement('div');
    wrap.className = `ivx-msg ${role === 'user' ? 'user' : 'bot'}`;

    const initials = role === 'user'
        ? "{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}"
        : '🤖';

    wrap.innerHTML = `
        <div class="ivx-msg-avatar ${role === 'user' ? 'user-av' : 'bot'}">${initials}</div>
        <div class="ivx-bubble-msg">${ivxFormat(text)}</div>
    `;
    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
}

function ivxShowTyping() {
    const container = document.getElementById('ivx-messages');
    const id = 'typing-' + Date.now();
    const wrap = document.createElement('div');
    wrap.id = id;
    wrap.className = 'ivx-msg bot';
    wrap.innerHTML = `
        <div class="ivx-msg-avatar bot">🤖</div>
        <div class="ivx-bubble-msg" style="padding:0">
            <div class="ivx-typing">
                <span></span><span></span><span></span>
            </div>
        </div>
    `;
    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
    return id;
}

function ivxRemoveTyping(id) {
    document.getElementById(id)?.remove();
}

function ivxFormat(text) {
    // Basic markdown: bold, newlines
    return text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
}
})();
</script>