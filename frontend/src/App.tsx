import { useCallback, useEffect, useState } from "react";
import Sidebar from "./components/layout/Sidebar";
import Topbar from "./components/layout/Topbar";
import { getAnalytics } from "./api/analytics";
import type { AnalyticsResponse } from "./types/analytics";
import "./App.css";

function App() {
  const [analytics, setAnalytics] = useState<AnalyticsResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchAnalytics = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const data = await getAnalytics("doliv");
      setAnalytics(data);
    } catch (err) {
      setError(
        err instanceof Error ? err.message : "Failed to fetch analytics",
      );
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void fetchAnalytics();
  }, [fetchAnalytics]);

  return (
    <div className="app-layout">
      <Sidebar />

      <main className="app-main">
        <Topbar onRefresh={fetchAnalytics} refreshing={loading} />

        <section className="dashboard-content">
          {loading && <p>Loading your analytics...</p>}

          {error && (
            <div className="dashboard-error" role="alert">
              <p>Unable to load analytics</p>
              <p>{error}</p>
              <button onClick={fetchAnalytics}>Try again</button>
            </div>
          )}

          {analytics && !loading && (
            <div>
              <p>Analytics loaded successfully.</p>
              <p>
                {analytics.overview.solved_problems} problems solved ·{" "}
                {analytics.overview.total_submissions} submissions
              </p>
            </div>
          )}
        </section>
      </main>
    </div>
  );
}

export default App;
