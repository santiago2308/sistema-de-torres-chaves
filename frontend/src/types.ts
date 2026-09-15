export interface Service {
  id: number
  name: string
  category: string
  description: string
  price: number
  price_formatted: string
}

export interface PaginatedResponse<T> {
  data: T[]
  links: Record<string, string | null>
  meta: unknown
}

export interface ChatMessage {
  id?: number
  role: 'user' | 'bot'
  content: string
  created_at?: string
  show_whatsapp?: boolean
}

export interface ChatResponse {
  data: {
    message: string
    show_whatsapp: boolean
    session_id: string
  }
}