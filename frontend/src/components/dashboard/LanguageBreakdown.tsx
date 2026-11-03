import { Card, ProgressBar } from "react-bootstrap";
import type { LanguageStats } from "../../types/analytics";
import "./DashboardCharts.css";

interface LanguageBreakdownProps {
  data: Record<string, LanguageStats>;
}

function LanguageBreakdown({ data }: LanguageBreakdownProps) {
  const languages = Object.entries(data).sort(
    ([, a], [, b]) => b.total_submissions - a.total_submissions,
  );

  const totalSubmissions = languages.reduce(
    (sum, [, stats]) => sum + stats.total_submissions,
    0,
  );
  if (languages.length === 0) {
    return (
      <Card className="language-breakdown">
        <Card.Body>
          <h3>Language breakdown</h3>
          <p className="language-breakdown-empty">
            No language statistics available yet.
          </p>
        </Card.Body>
      </Card>
    );
  }

  return (
    <Card className="language-breakdown">
      <Card.Body>
        <div className="language-breakdown-header">
          <div>
            <h3>Language breakdown</h3>
            <p>Submission performance by programming language</p>
          </div>
          <span>
            {languages.length} languages · {totalSubmissions} submissions
          </span>
        </div>

        <div className="language-breakdown-list">
          {languages.map(([language, stats]) => {
            const successRate =
              stats.total_submissions > 0
                ? (stats.accepted_submissions / stats.total_submissions) * 100
                : 0;

            return (
              <div className="language-breakdown-item" key={language}>
                <div className="language-breakdown-info">
                  <strong>{language}</strong>
                  <span>
                    {stats.accepted_submissions} accepted /{" "}
                    {stats.total_submissions} submissions
                  </span>
                </div>

                <div className="language-breakdown-progress">
                  <ProgressBar
                    now={successRate}
                    variant="success"
                    aria-label={`${language} success rate`}
                  />
                  <strong>{successRate.toFixed(1)}%</strong>
                </div>
              </div>
            );
          })}
        </div>
      </Card.Body>
    </Card>
  );
}

export default LanguageBreakdown;
