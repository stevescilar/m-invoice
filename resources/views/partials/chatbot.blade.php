{{--
Invoxa AI Copilot Widget (Powered by Google Gemini)
Include in layouts: app.blade.php and welcome.blade.php
Just before </body>:  @include('partials.chatbot')
--}}

<style>
/* ── Bubble ── */
#ivx-bubble {
    position: fixed; bottom: 24px; right: 24px; z-index: 9998;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #15803d);
    box-shadow: 0 6px 24px rgba(22,163,74,0.45);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    border: none; outline: none;
}
#ivx-bubble:hover { transform: scale(1.08); background: #15803d; }
#ivx-bubble.open { transform: scale(0.92); background: #111827; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
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
    width: 380px; max-width: calc(100vw - 32px);
    height: 550px; max-height: calc(100vh - 120px);
    background: #0d1410;
    border: 1px solid rgba(22,163,74,0.25);
    border-radius: 20px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.04);
    display: flex; flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    transition: all 0.28s cubic-bezier(0.34,1.56,0.64,1);
    transform: scale(0.85) translateY(20px);
    opacity: 0;
    pointer-events: none;
}
#ivx-chat.open {
    transform: scale(1) translateY(0);
    opacity: 1;
    pointer-events: all;
}
#ivx-chat.expanded {
    width: 520px;
    height: 650px;
}

/* ── Header ── */
.ivx-header {
    background: linear-gradient(135deg, #0f1f14, #0d1a10);
    border-bottom: 1px solid rgba(22,163,74,0.18);
    padding: 14px 16px;
    display: flex; align-items: center; gap: 11px;
    flex-shrink: 0;
}
.ivx-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.2);
}
.ivx-avatar svg { width: 19px; height: 19px; color: #fff; }
.ivx-header-info { flex: 1; min-width: 0; }
.ivx-name {
    font-size: 14px; font-weight: 700; color: #fff; letter-spacing: -0.2px;
    display: flex; align-items: center; gap: 6px;
}
.ivx-badge {
    font-size: 9.5px; font-weight: 700; text-transform: uppercase;
    padding: 1.5px 6px; border-radius: 99px;
    background: rgba(34,197,94,0.18); color: #86efac; border: 1px solid rgba(34,197,94,0.3);
}
.ivx-status {
    font-size: 11px; color: #4ade80;
    display: flex; align-items: center; gap: 4px; margin-top: 1px;
}
.ivx-status::before {
    content: ''; width: 6px; height: 6px; background: #4ade80;
    border-radius: 50%; animation: ivx-blink 2s infinite;
}
@keyframes ivx-blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
.ivx-hdr-btn {
    width: 28px; height: 28px; border-radius: 8px;
    background: rgba(255,255,255,0.06); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.55); transition: all 0.15s;
}
.ivx-hdr-btn:hover { background: rgba(255,255,255,0.12); color: #fff; }

/* ── Messages ── */
.ivx-messages {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 12px;
    scroll-behavior: smooth;
}
.ivx-messages::-webkit-scrollbar { width: 4px; }
.ivx-messages::-webkit-scrollbar-track { background: transparent; }
.ivx-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 10px; }

.ivx-msg { display: flex; gap: 8px; animation: ivx-msgIn 0.22s ease; }
@keyframes ivx-msgIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.ivx-msg.user { flex-direction: row-reverse; }

.ivx-msg-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800;
}
.ivx-msg-avatar.bot { background: rgba(22,163,74,0.18); color: #4ade80; }
.ivx-msg-avatar.user-av { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.75); }

.ivx-bubble-wrap { max-width: 83%; display: flex; flex-direction: column; gap: 4px; }
.ivx-bubble-msg {
    padding: 10px 14px; border-radius: 14px;
    font-size: 13px; line-height: 1.55; word-break: break-word;
}
.ivx-msg.bot .ivx-bubble-msg {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.9);
    border-bottom-left-radius: 4px;
}
.ivx-msg.user .ivx-bubble-msg {
    background: #16a34a;
    color: #fff;
    border-bottom-right-radius: 4px;
}

.ivx-doc-link {
    display: inline-flex; align-items: center; gap: 3px;
    background: rgba(34,197,94,0.18); color: #86efac;
    border: 1px solid rgba(34,197,94,0.35);
    padding: 1px 7px; border-radius: 6px;
    font-weight: 600; font-size: 12px; text-decoration: none;
    transition: all 0.15s;
}
.ivx-doc-link:hover { background: rgba(34,197,94,0.3); color: #fff; }

.ivx-copy-reply {
    align-self: flex-start;
    background: transparent; border: none; cursor: pointer;
    color: rgba(255,255,255,0.35); font-size: 11px;
    padding: 2px 4px; transition: color 0.15s;
}
.ivx-copy-reply:hover { color: #4ade80; }

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
    padding: 0 14px 10px;
    display: flex; gap: 6px; flex-wrap: wrap;
}
.ivx-quick button {
    font-size: 11.5px; padding: 5px 10px; border-radius: 20px;
    background: rgba(22,163,74,0.12); border: 1px solid rgba(22,163,74,0.25);
    color: #86efac; cursor: pointer; transition: all 0.15s; white-space: nowrap;
}
.ivx-quick button:hover { background: rgba(22,163,74,0.25); color: #fff; }

/* ── Input ── */
.ivx-input-row {
    padding: 12px 14px;
    border-top: 1px solid rgba(255,255,255,0.07);
    display: flex; gap: 8px; align-items: flex-end;
    background: rgba(255,255,255,0.02);
    flex-shrink: 0;
}
#ivx-input {
    flex: 1; background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px; padding: 10px 13px;
    color: #fff; font-size: 13px; resize: none;
    outline: none; transition: border-color 0.2s;
    font-family: inherit; line-height: 1.4;
    max-height: 100px; min-height: 40px;
}
#ivx-input::placeholder { color: rgba(255,255,255,0.3); }
#ivx-input:focus { border-color: rgba(22,163,74,0.5); }
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
<button id="ivx-bubble" aria-label="Open Invoxa AI Copilot" onclick="ivxToggle()">
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
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
        </div>
        <div class="ivx-header-info">
            <div class="ivx-name">
                <span>Invoxa AI</span>
                <span class="ivx-badge">Gemini</span>
            </div>
            <div class="ivx-status">Connected to your live business data</div>
        </div>
        <button class="ivx-hdr-btn" onclick="ivxResetChat()" title="Clear conversation">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
        <button class="ivx-hdr-btn hidden sm:flex" onclick="ivxExpandToggle()" title="Toggle wider view">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
            </svg>
        </button>
        <button class="ivx-hdr-btn" onclick="ivxToggle()" aria-label="Close">
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
        <button onclick="ivxQuick('Give me a summary of my revenue, profit, expenses, and VAT this month.')">📊 Monthly Summary</button>
        <button onclick="ivxQuick('Which clients have overdue or unpaid balances right now?')">⚠️ Who owes me?</button>
        <button onclick="ivxQuick('Draft a 4-camera CCTV installation quotation using my catalog prices.')">📋 Draft CCTV Quote</button>
        <button onclick="ivxQuick('Write a polite WhatsApp payment reminder for my overdue invoices.')">✍️ Payment Follow-up</button>
        @else
        <button onclick="ivxQuick('What does Invoxa do?')">✨ What is Invoxa?</button>
        <button onclick="ivxQuick('How much does Invoxa cost?')">💰 Pricing</button>
        <button onclick="ivxQuick('Does Invoxa support M-Pesa and KRA ETR?')">📱 M-Pesa &amp; ETR</button>
        <button onclick="ivxQuick('How do I get started?')">🚀 Get started</button>
        @endauth
    </div>

    {{-- Input --}}
    <div class="ivx-input-row">
        <textarea id="ivx-input" placeholder="Ask about invoices, profits, quotes, or catalog prices…" rows="1"
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
    let chatExpanded = false;
    let history  = [];
    let isLoading = false;

    function ivxWelcomeMessage() {
        @auth
        const name = "{{ auth()->user()->name ?? 'there' }}".split(' ')[0];
        ivxAddMsg('bot', `Hey **${name}**! 👋 I'm your **Invoxa AI Copilot** powered by Google Gemini.\n\nI'm connected to your live invoices, quotations, expenses, and catalog prices. Ask me for financial summaries, who owes you money, or to draft a quote!`);
        @else
        ivxAddMsg('bot', "Hi! 👋 I'm the **Invoxa AI Assistant**. I can tell you about features, pricing, M-Pesa setup, and more. How can I help?");
        @endauth
    }

    window.addEventListener('DOMContentLoaded', () => {
        ivxWelcomeMessage();
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
            setTimeout(() => document.getElementById('ivx-input')?.focus(), 250);
        }
    };

    window.ivxExpandToggle = function() {
        chatExpanded = !chatExpanded;
        document.getElementById('ivx-chat').classList.toggle('expanded', chatExpanded);
    };

    window.ivxResetChat = function() {
        history = [];
        document.getElementById('ivx-messages').innerHTML = '';
        document.getElementById('ivx-quick').style.display = 'flex';
        ivxWelcomeMessage();
    };

    window.ivxQuick = function(text) {
        document.getElementById('ivx-input').value = text;
        document.getElementById('ivx-quick').style.display = 'none';
        ivxSend();
    };

    window.ivxCopyText = function(btn, encodedText) {
        const raw = decodeURIComponent(encodedText);
        navigator.clipboard.writeText(raw);
        const orig = btn.textContent;
        btn.textContent = '✅ Copied';
        setTimeout(() => btn.textContent = orig, 2000);
    };

    window.ivxSend = async function() {
        if (isLoading) return;
        const input = document.getElementById('ivx-input');
        const msg   = input.value.trim();
        if (!msg) return;

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
                body: JSON.stringify({ message: msg, history: history.slice(-12) }),
            });

            ivxRemoveTyping(typingId);
            const data = await res.json();

            if (data.error) {
                ivxAddMsg('bot', '⚠️ ' + data.error);
            } else if (data.reply && String(data.reply).trim() !== '') {
                ivxAddMsg('bot', data.reply, true);
                history.push({ role: 'user', content: msg });
                history.push({ role: 'model', content: data.reply });
                if (history.length > 24) history = history.slice(-24);
            } else {
                ivxAddMsg('bot', '⚠️ Gemini did not return text for this message. Please try asking in a different way or check your GEMINI_API_KEY in .env.');
            }
        } catch(e) {
            ivxRemoveTyping(typingId);
            ivxAddMsg('bot', '⚠️ Network error. Please check your connection and try again.');
        }

        isLoading = false;
        document.getElementById('ivx-send').disabled = false;
        input.focus();
    };

    function ivxAddMsg(role, text, allowCopy = false) {
        const container = document.getElementById('ivx-messages');
        const wrap = document.createElement('div');
        wrap.className = `ivx-msg ${role === 'user' ? 'user' : 'bot'}`;

        const initials = role === 'user'
            ? "{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}"
            : '✨';

        const copyBtnHtml = (role === 'bot' && allowCopy)
            ? `<button class="ivx-copy-reply" onclick="ivxCopyText(this, '${encodeURIComponent(text)}')">📋 Copy reply</button>`
            : '';

        wrap.innerHTML = `
            <div class="ivx-msg-avatar ${role === 'user' ? 'user-av' : 'bot'}">${initials}</div>
            <div class="ivx-bubble-wrap">
                <div class="ivx-bubble-msg">${ivxFormat(text)}</div>
                ${copyBtnHtml}
            </div>
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
            <div class="ivx-msg-avatar bot">✨</div>
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
        let safe = String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Convert markdown links [LABEL](/path) to clickable pill links
        safe = safe.replace(/\[([^\]]+)\]\((\/[a-zA-Z0-9_\-\/]+)\)/g, '<a href="$2" class="ivx-doc-link">$1 ↗</a>');

        // Bold, italic, inline code
        safe = safe
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`([^`]+)`/g, '<code style="background:rgba(255,255,255,0.1);padding:1px 5px;border-radius:4px;font-size:12px;">$1</code>');

        // Bullet points
        safe = safe.replace(/^- (.*)$/gm, '• $1');

        return safe.replace(/\n/g, '<br>');
    }
})();
</script>