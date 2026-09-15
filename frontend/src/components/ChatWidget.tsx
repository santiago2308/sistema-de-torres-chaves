import { useEffect, useRef, useState } from 'react'
import { sendChatMessage, WHATSAPP_NUMBER } from '../api'
import type { ChatMessage } from '../types'
import WhatsAppButton from './WhatsAppButton'

function generateSessionId(): string {
  if (crypto.randomUUID) {
    return crypto.randomUUID()
  }
  return `${Date.now()}-${Math.random().toString(36).slice(2)}`
}

const SUGGESTIONS = [
  '¿Qué servicios tienen?',
  '¿Cuánto cuesta la iluminación para un evento?',
  '¿Cómo reservo?',
  '¿Hablan por WhatsApp?',
]

function ChatWidget() {
  const [open, setOpen] = useState(false)
  const [messages, setMessages] = useState<ChatMessage[]>([
    {
      role: 'bot',
      content:
        '¡Hola! 👋 Soy el asistente del catálogo. Pregúntame por nuestros servicios, precios o cómo reservar.',
    },
  ])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const sessionIdRef = useRef<string>(generateSessionId())
  const listRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    listRef.current?.scrollTo({ top: listRef.current.scrollHeight, behavior: 'smooth' })
  }, [messages, loading, open])

  const send = async (text?: string) => {
    const content = (text ?? input).trim()
    if (!content || loading) return

    setInput('')
    const message: ChatMessage = { role: 'user', content }
    setMessages((prev) => [...prev, message])
    setLoading(true)

    try {
      const bot = await sendChatMessage(sessionIdRef.current, content)
      setMessages((prev) => [
        ...prev,
        { role: 'bot', content: bot.message, show_whatsapp: bot.show_whatsapp },
      ])
    } catch {
      setMessages((prev) => [
        ...prev,
        {
          role: 'bot',
          content:
            'Lo siento, tuve un problema para responder. Inténtalo de nuevo o escríbenos por WhatsApp. 🙏',
          show_whatsapp: true,
        },
      ])
    } finally {
      setLoading(false)
    }
  }

  return (
    <>
      {!open && (
        <button
          type="button"
          className="chat-fab"
          onClick={() => setOpen(true)}
          aria-label="Abrir chat de asistencia"
        >
          <ChatIcon />
          <span className="chat-fab-pulse" />
        </button>
      )}

      {open && (
        <div className="chat-window" role="dialog" aria-label="Chat de asistencia">
          <header className="chat-header">
            <strong>Asistente de eventos</strong>
            <button type="button" onClick={() => setOpen(false)} aria-label="Cerrar chat">
              ✕
            </button>
          </header>

          <div className="chat-messages" ref={listRef}>
            {messages.map((msg, i) => (
              <div key={i} className={`chat-msg ${msg.role}`}>
                <p>{msg.content}</p>
                {msg.role === 'bot' && msg.show_whatsapp && WHATSAPP_NUMBER && (
                  <div className="chat-msg-whatsapp">
                    <WhatsAppButton text="Chatear con asesor" className="chat-inline-wa" />
                  </div>
                )}
              </div>
            ))}
            {loading && (
              <div className="chat-msg bot">
                <p className="chat-typing">Escribiendo…</p>
              </div>
            )}
          </div>

          {messages.length <= 1 && (
            <div className="chat-suggestions">
              {SUGGESTIONS.map((s) => (
                <button key={s} type="button" onClick={() => void send(s)}>
                  {s}
                </button>
              ))}
            </div>
          )}

          <form
            className="chat-input-form"
            onSubmit={(e) => {
              e.preventDefault()
              void send()
            }}
          >
            <input
              value={input}
              onChange={(e) => setInput(e.target.value)}
              placeholder="Escribe tu pregunta…"
              aria-label="Mensaje"
            />
            <button type="submit" disabled={loading || !input.trim()} aria-label="Enviar">
              ➤
            </button>
          </form>
        </div>
      )}
    </>
  )
}

export function ChatIcon() {
  return (
    <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor" aria-hidden="true">
      <path d="M12 3C6.48 3 2 6.9 2 11.7c0 2.7 1.34 5.1 3.46 6.74L4.4 21.4l4.06-1.5c1.12.37 2.33.5 3.54.5 5.52 0 10-3.9 10-8.7S17.52 3 12 3Zm-1 11.5-2.7-2.7 1.44-1.44 1.26 1.26 2.52-2.52 1.44 1.44-3.96 3.96Zm0 0" />
    </svg>
  )
}

export default ChatWidget