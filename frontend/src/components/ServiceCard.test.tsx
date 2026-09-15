import { render, screen } from '@testing-library/react'
import { describe, expect, it, vi } from 'vitest'
import ServiceCard from './ServiceCard'
import type { Service } from '../types'

describe('ServiceCard', () => {
  const service: Service = {
    id: 1,
    name: 'Sonido Básico',
    category: 'Sonido',
    description: '2 parlantes y consola.',
    price: 120000,
    price_formatted: '120.000',
  }

  it('renders the service name, category, description and price', () => {
    render(<ServiceCard service={service} />)

    expect(screen.getByRole('heading', { name: /Sonido Básico/ })).toBeInTheDocument()
    expect(screen.getByText('Sonido')).toBeInTheDocument()
    expect(screen.getByText(/2 parlantes y consola/)).toBeInTheDocument()
    expect(screen.getByText('$120.000')).toBeInTheDocument()
    expect(vi.isMockFunction(service.name)).toBe(false)
  })
})