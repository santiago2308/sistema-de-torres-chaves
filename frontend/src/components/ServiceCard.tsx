import type { Service } from '../types'

interface Props {
  service: Service
}

function ServiceCard({ service }: Props) {
  return (
    <article className="card">
      <span className="card-category">{service.category}</span>
      <h3>{service.name}</h3>
      <p className="card-description">{service.description}</p>
      <div className="card-footer">
        <span className="card-price">${service.price_formatted}</span>
      </div>
    </article>
  )
}

export default ServiceCard