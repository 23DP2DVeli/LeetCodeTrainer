import { Card } from "react-bootstrap";
import type { LucideIcon } from "lucide-react";
import "./StatCard.css";

interface StatCardProps {
  title: string;
  value: string | number;
  icon: LucideIcon;
  accent: "blue" | "green" | "orange" | "red";
}

function StatCard({ title, value, icon: Icon, accent }: StatCardProps) {
  return (
    <Card className={`stat-card stat-card-${accent} h-100`}>
      <Card.Body>
        <div className="stat-card-content">
          <div>
            <p className="stat-card-label">{title}</p>
            <h2 className="stat-card-value">{value}</h2>
          </div>

          <div className="stat-card-icon">
            <Icon size={20} strokeWidth={1.8} />
          </div>
        </div>
      </Card.Body>
    </Card>
  );
}

export default StatCard;
