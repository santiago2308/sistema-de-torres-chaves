import { useEffect, useRef, useState } from 'react'
import { fetchServices } from '../api'
import type { Service } from '../types'
import ServiceCard from './ServiceCard'

interface Props {
  categories?: string[]
}

interface Pagination {
  current: number
  last: number
  next: string | null
  prev: string | null
}

const CATEGORIES = ['Todos', 'Sonido', 'Iluminación', 'DJ', 'Pantalla LED', 'Efectos', 'Packs']

function ServiceCatalog({ categories = CATEGORIES }: Props) {
  const [services, setServices] = useState<Service[]>([])
  const [category, setCategory] = useState('Todos')
  const [pagination, setPagination] = useState<Pagination>({ current: 1, last: 1, next: null, prev: null })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const mounted = useRef(true)

  useEffect(() => {
    return () => {
      mounted.current = false
    }
  }, [])

  useEffect(() => {
    const load = async () => {
      setLoading(true)
      setError(null)
      try {
        const result = await fetchServices(category, 1)
        if (!mounted.current) return
        setServices(result.data)
        setPagination({
          current: 1,
          last: lastPage(result),
          next: nextPage(result),
          prev: prevPage(result),
        })
      } catch (e) {
        if (!mounted.current) return
        setError(e instanceof Error ? e.message : 'Error inesperado')
      } finally {
        if (mounted.current) setLoading(false)
      }
    }
    void load()
  }, [category])

  const changePage = async (page: number) => {
    setLoading(true)
    try {
      const result = await fetchServices(category, page)
      setServices(result.data)
      setPagination({
        current: page,
        last: lastPage(result),
        next: nextPage(result),
        prev: prevPage(result),
      })
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Error inesperado')
    } finally {
      setLoading(false)
    }
  }

  return (
    <section id="catalogo" className="catalog">
      <div className="catalog-header">
        <h2>Nuestros servicios</h2>
        <p>Equipos de sonido, iluminación y tecnología para tus eventos.</p>
      </div>

      <div className="catalog-filters" role="group" aria-label="Filtrar por categoría">
        {categories.map((cat) => (
          <button
            key={cat}
            type="button"
            className={`filter-chip ${category === cat ? 'active' : ''}`}
            onClick={() => setCategory(cat)}
          >
            {cat}
          </button>
        ))}
      </div>

      {error && <p className="catalog-error">{error}</p>}

      {loading ? (
        <div className="catalog-grid catalog-skeleton">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="card skeleton" />
          ))}
        </div>
      ) : (
        <>
          <div className="catalog-grid">
            {services.map((service) => (
              <ServiceCard key={service.id} service={service} />
            ))}
          </div>

          {pagination.last > 1 && (
            <div className="catalog-pagination">
              <button
                type="button"
                disabled={!pagination.prev}
                onClick={() => pagination.prev && changePage(pagination.current - 1)}
              >
                ← Anterior
              </button>
              <span>
                Página {pagination.current} de {pagination.last}
              </span>
              <button
                type="button"
                disabled={!pagination.next}
                onClick={() => pagination.next && changePage(pagination.current + 1)}
              >
                Siguiente →
              </button>
            </div>
          )}
        </>
      )}
    </section>
  )
}

function lastPage(result: { meta?: unknown }): number {
  const meta = result.meta as { last_page?: number }
  return meta?.last_page ?? 1
}

function nextPage(result: { links?: Record<string, string | null> }): string | null {
  return result.links?.next ?? null
}

function prevPage(result: { links?: Record<string, string | null> }): string | null {
  return result.links?.prev ?? null
}

export default ServiceCatalog