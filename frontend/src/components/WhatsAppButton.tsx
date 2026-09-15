import { WHATSAPP_NUMBER } from '../api'

interface Props {
  text?: string
  className?: string
}

function WhatsAppButton({ text = 'Contáctanos por WhatsApp', className = '' }: Props) {
  if (!WHATSAPP_NUMBER) {
    return null
  }

  const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent('Hola, vengo del catálogo y quiero información sobre sus servicios.')}`

  return (
    <a
      href={url}
      target="_blank"
      rel="noopener noreferrer"
      className={`whatsapp-button ${className}`}
      aria-label="Contactar por WhatsApp"
    >
      <WhatsAppIcon />
      <span>{text}</span>
    </a>
  )
}

export function WhatsAppIcon() {
  return (
    <svg viewBox="0 0 32 32" width="22" height="22" fill="currentColor" aria-hidden="true">
      <path d="M16.02 3c-7.16 0-13 5.84-13 13a12.97 12.97 0 0 0 1.8 6.55L3 29l6.6-1.72a13 13 0 0 0 6.42 1.72h.01c7.16 0 12.99-5.84 12.99-13S23.18 3 16.02 3Zm0 23.8h-.01a10.8 10.8 0 0 1-5.5-1.5l-.4-.24-3.67.96.97-3.58-.26-.42a10.8 10.8 0 0 1-1.67-5.81c0-5.95 4.85-10.8 10.8-10.8a10.8 10.8 0 0 1 10.79 10.8c0 5.96-4.84 10.8-10.05 10.79Zm5.92-8.1c-.32-.16-1.9-.94-2.2-1.05-.29-.11-.5-.16-.72.16-.21.32-.82 1.05-1.01 1.26-.18.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.58-.95-.84-1.59-1.88-1.78-2.2-.18-.32-.02-.49.14-.65.15-.14.32-.37.48-.56.16-.19.22-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.53-.53-.72-.54h-.61c-.21 0-.56.08-.85.4-.29.32-1.11 1.09-1.11 2.65s1.14 3.07 1.3 3.29c.16.21 2.24 3.42 5.43 4.8.76.33 1.35.52 1.81.67.76.24 1.45.21 2 .13.61-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.14-.29-.22-.61-.37Z" />
    </svg>
  )
}

export default WhatsAppButton