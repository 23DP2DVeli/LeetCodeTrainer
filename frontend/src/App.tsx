import { useEffect, useState } from 'react'
import './App.css'

interface HealthResponse {
  status: string
  service: string
  timestamp: string
}

function App() {
  const [healthStatus, setHealthStatus] = useState<HealthResponse | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const checkBackendHealth = async () => {
      try {
        const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
        const response = await fetch(`${apiUrl}/health`)

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()
        setHealthStatus(data)
        setError(null)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to connect to backend')
        setHealthStatus(null)
      } finally {
        setLoading(false)
      }
    }

    checkBackendHealth()
  }, [])

  return (
    <div className="App">
      <header className="App-header">
        <h1>LeetCodeTrainer</h1>
        <p>Personalized Algorithmic Learning Platform</p>

        <div className="health-check">
          <h2>Backend Health Check</h2>
          {loading && <p>Checking backend connection...</p>}

          {error && (
            <div className="error">
              <p>❌ Backend connection failed</p>
              <p className="error-detail">{error}</p>
              <p className="help-text">
                Make sure the Laravel backend is running on port 8000:<br/>
                <code>cd backend && php artisan serve</code>
              </p>
            </div>
          )}

          {healthStatus && (
            <div className="success">
              <p>✅ Backend connected successfully</p>
              <div className="health-details">
                <p><strong>Service:</strong> {healthStatus.service}</p>
                <p><strong>Status:</strong> {healthStatus.status}</p>
                <p><strong>Timestamp:</strong> {new Date(healthStatus.timestamp).toLocaleString()}</p>
              </div>
            </div>
          )}
        </div>

        <div className="next-steps">
          <h2>Setup Complete ✓</h2>
          <p>The project foundation is ready. Next steps:</p>
          <ul>
            <li>Review <code>docs/DESIGN_GUIDELINES.md</code> before building UI</li>
            <li>Configure database in <code>backend/.env</code></li>
            <li>Implement Phase 1: Data Foundation (see CLAUDE.md)</li>
          </ul>
        </div>
      </header>
    </div>
  )
}

export default App
