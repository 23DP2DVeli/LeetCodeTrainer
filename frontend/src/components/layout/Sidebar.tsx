import {
  LayoutDashboard,
  Dumbbell,
  Brain,
  TrendingUp,
  Settings,
} from "lucide-react";
import "./Sidebar.css";

const navigation = [
  { label: "Dashboard", icon: LayoutDashboard, active: true },
  { label: "Practice", icon: Dumbbell, active: false },
  { label: "Skills", icon: Brain, active: false },
  { label: "Progress", icon: TrendingUp, active: false },
];

function Sidebar() {
  return (
    <aside className="sidebar">
      <div className="sidebar-brand">
        <div className="sidebar-brand-icon">L</div>
        <span>LeetCodeTrainer</span>
      </div>

      <div className="sidebar-section-label">WORKSPACE</div>

      <nav className="sidebar-navigation" aria-label="Main navigation">
        {navigation.map(({ label, icon: Icon, active }) => (
          <div
            key={label}
            className={`sidebar-item ${active ? "active" : ""}`}
            aria-current={active ? "page" : undefined}
          >
            <Icon size={18} strokeWidth={1.8} />
            <span>{label}</span>
          </div>
        ))}
      </nav>

      <div className="sidebar-bottom">
        <div className="sidebar-item">
          <Settings size={18} strokeWidth={1.8} />
          <span>Settings</span>
        </div>
      </div>
    </aside>
  );
}

export default Sidebar;
