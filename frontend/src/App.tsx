import { Suspense, lazy, useCallback, useEffect, useState } from "react";
import { Row, Col } from "react-bootstrap";
import { Code2, Send, Target, CheckCircle2 } from "lucide-react";

import Sidebar from "./components/layout/Sidebar";
import Topbar from "./components/layout/Topbar";
import StatCard from "./components/dashboard/StatCard";

const PerformanceTrend = lazy(
  () => import("./components/dashboard/PerformanceTrend"),
);

const ActivityHeatmap = lazy(
  () => import("./components/dashboard/ActivityHeatmap"),
);
const DifficultyChart = lazy(
  () => import("./components/dashboard/DifficultyChart"),
);

const LanguageBreakdown = lazy(
  () => import("./components/dashboard/LanguageBreakdown"),
);

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
            <>
              <Row className="g-3">
                <Col xs={12} sm={6} xl={3}>
                  <StatCard
                    title="Problems solved"
                    value={analytics.overview.solved_problems}
                    icon={Code2}
                    accent="blue"
                  />
                </Col>

                <Col xs={12} sm={6} xl={3}>
                  <StatCard
                    title="Submissions"
                    value={analytics.overview.total_submissions}
                    icon={Send}
                    accent="orange"
                  />
                </Col>

                <Col xs={12} sm={6} xl={3}>
                  <StatCard
                    title="Success rate"
                    value={`${analytics.overview.success_rate}%`}
                    icon={Target}
                    accent="green"
                  />
                </Col>

                <Col xs={12} sm={6} xl={3}>
                  <StatCard
                    title="Accepted"
                    value={analytics.overview.accepted_submissions}
                    icon={CheckCircle2}
                    accent="green"
                  />
                </Col>
              </Row>
              <Suspense fallback={<p>Loading charts...</p>}>
                <div className="mt-4">
                  <ActivityHeatmap data={analytics.daily_activity} />
                </div>
                <div className="mt-4">
                  <PerformanceTrend data={analytics.daily_activity} />
                </div>

                <Row className="g-3 mt-1">
                  <Col xs={12} xl={6}>
                    <DifficultyChart data={analytics.difficulty_breakdown} />
                  </Col>

                  <Col xs={12} xl={6}>
                    <LanguageBreakdown data={analytics.language_breakdown} />
                  </Col>
                </Row>
              </Suspense>
            </>
          )}
        </section>
      </main>
    </div>
  );
}

export default App;
