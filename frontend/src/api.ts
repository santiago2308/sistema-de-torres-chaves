import type { ChatResponse, PaginatedResponse, Service } from './types'

const API_URL = import.meta.env.VITE_API_URL ?? 'http://127.0.0.1:8000/api/v1'

export const WHATSAPP_NUMBER: string =
  import.meta.env.VITE_WHATSAPP_NUMBER ?? ''

function buildUrl(path: string, params?: Record<string, string | number>): string {
  const url = new URL(`${API_URL}${path}`)
  if (params) {
    Object.entries(params).forEach(([key, value]) => {
      url.searchParams.set(key, String(value))
    })
  }
  return url.toString()
}

export async function fetchServices(
  category?: string,
  page = 1,
): Promise<PaginatedResponse<Service>> {
  const params: Record<string, string | number> = { page }
  if (category && category !== 'Todos') {
    params.category = category
  }
  const res = await fetch(buildUrl('/services', params))
  if (!res.ok) {
    throw new Error(`Error al cargar los servicios (${res.status})`)
  }
  return res.json()
}

export async function sendChatMessage(
  sessionId: string,
  message: string,
): Promise<ChatResponse['data']> {
  const res = await fetch(buildUrl('/chat/message'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ session_id: sessionId, message }),
  })
  if (!res.ok) {
    throw new Error(`Error al enviar el mensaje (${res.status})`)
  }
  const json = (await res.json()) as ChatResponse
  return json.data
}