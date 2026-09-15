import ChatWidget from './components/ChatWidget'
import ServiceCatalog from './components/ServiceCatalog'
import WhatsAppButton from './components/WhatsAppButton'
import './App.css'

function App() {
  return (
    <div className="app">
      <header className="app-header">
        <div className="app-header-inner">
          <span className="app-brand">🎛️ Catálogo de Servicios</span>
          <nav className="app-nav" aria-label="Navegación">
            <a href="#catalogo">Servicios</a>
            <a href="/catalogo/chat" onClick={(e) => e.preventDefault()}>
              Asistente
            </a>
          </nav>
        </div>
      </header>

      <main>
        <section className="hero">
          <h1>Sonido, iluminación y tecnología para tu evento</h1>
          <p>
            Equipos profesionales de sonido, iluminación, pantallas LED y efectos
            especiales. Consulta el catálogo y reserva tu fecha.
          </p>
          <div className="hero-actions">
            <a href="#catalogo" className="hero-cta">
              Ver catálogo
            </a>
            <WhatsAppButton text="Consulta por WhatsApp" className="hero-wa" />
          </div>
        </section>

        <ServiceCatalog />

        <section className="about">
          <h2>¿Dónde hacemos eventos?</h2>
          <p>
            Cobertura en la zona. Si no conoces tu fecha o cobertura, el asistente o nuestro
            equipo por WhatsApp puede ayudarte a confirmar disponibilidad.
          </p>
          <WhatsAppButton text="Preguntar disponibilidad" className="about-wa" />
        </section>
      </main>

      <footer className="app-footer">
        <p>© {new Date().getFullYear()} Catálogo de Servicios. Todos los derechos reservados.</p>
      </footer>

      <ChatWidget />
    </div>
  )
}

export default App