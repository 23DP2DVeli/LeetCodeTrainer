import { Row, Col } from "react-bootstrap";
import "./DashboardSkeleton.css";

export function DashboardSkeleton() {
  return (
    <div className="dashboard-skeleton" aria-label="Loading dashboard">
      <Row className="g-3">
        {Array.from({ length: 4 }).map((_, index) => (
          <Col xs={12} sm={6} xl={3} key={index}>
            <div className="skeleton-card">
              <div className="skeleton-line skeleton-label" />
              <div className="skeleton-line skeleton-value" />
            </div>
          </Col>
        ))}
      </Row>

      <div className="skeleton-chart mt-4">
        <div className="skeleton-line skeleton-heading" />
        <div className="skeleton-line skeleton-subheading" />
        <div className="skeleton-chart-area" />
      </div>

      <div className="skeleton-chart mt-4">
        <div className="skeleton-line skeleton-heading" />
        <div className="skeleton-line skeleton-subheading" />
        <div className="skeleton-chart-area" />
      </div>

      <Row className="g-3 mt-1">
        <Col xs={12} xl={6}>
          <div className="skeleton-chart">
            <div className="skeleton-line skeleton-heading" />
            <div className="skeleton-chart-area skeleton-chart-small" />
          </div>
        </Col>

        <Col xs={12} xl={6}>
          <div className="skeleton-chart">
            <div className="skeleton-line skeleton-heading" />
            <div className="skeleton-chart-area skeleton-chart-small" />
          </div>
        </Col>
      </Row>
    </div>
  );
}

export function ChartsSkeleton() {
  return (
    <div className="charts-skeleton" aria-label="Loading charts">
      <div className="skeleton-chart">
        <div className="skeleton-line skeleton-heading" />
        <div className="skeleton-line skeleton-subheading" />
        <div className="skeleton-chart-area" />
      </div>

      <div className="skeleton-chart mt-4">
        <div className="skeleton-line skeleton-heading" />
        <div className="skeleton-line skeleton-subheading" />
        <div className="skeleton-chart-area" />
      </div>

      <Row className="g-3 mt-1">
        <Col xs={12} xl={6}>
          <div className="skeleton-chart">
            <div className="skeleton-line skeleton-heading" />
            <div className="skeleton-chart-area skeleton-chart-small" />
          </div>
        </Col>

        <Col xs={12} xl={6}>
          <div className="skeleton-chart">
            <div className="skeleton-line skeleton-heading" />
            <div className="skeleton-chart-area skeleton-chart-small" />
          </div>
        </Col>
      </Row>
    </div>
  );
}
