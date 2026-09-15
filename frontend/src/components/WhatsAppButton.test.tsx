import { render, screen } from '@testing-library/react'
import { beforeEach, describe, expect, it, vi } from 'vitest'

describe('WhatsAppButton', () => {
  beforeEach(() => {
    vi.resetModules()
  })

  it('renders the direct WhatsApp link with the configured number', async () => {
    vi.stubEnv('VITE_WHATSAPP_NUMBER', '5492646214599')
    const { default: Button } = await import('./WhatsAppButton')

    render(<Button />)

    const link = screen.getByRole('link', { name: /Contactar por WhatsApp/ })
    expect(link).toHaveAttribute(
      'href',
      'https://wa.me/5492646214599?text=Hola%2C%20vengo%20del%20cat%C3%A1logo%20y%20quiero%20informaci%C3%B3n%20sobre%20sus%20servicios.',
    )
  })

  it('renders nothing when the number is not configured', async () => {
    vi.stubEnv('VITE_WHATSAPP_NUMBER', '')
    const { default: Button } = await import('./WhatsAppButton')

    const { container } = render(<Button />)
    expect(container).toBeEmptyDOMElement()
  })
})