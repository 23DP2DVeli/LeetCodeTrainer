import { RefreshCw, ExternalLink } from "lucide-react";
import "./Topbar.css";

interface TopbarProps {
  onRefresh: () => void;
  refreshing: boolean;
}

function Topbar({ onRefresh, refreshing }: TopbarProps) {
  return (
    <header className="topbar">
      <div className="topbar-heading">
        <h1>Dashboard</h1>
        <p>Your algorithmic learning overview</p>
      </div>

      <div className="topbar-actions">
        <a
          className="topbar-profile"
          href="https://leetcode.com/u/doliv/"
          target="_blank"
          rel="noreferrer"
        >
          <span className="profile-avatar">D</span>
          <span className="profile-name">doliv</span>
          <ExternalLink size={14} />
        </a>

        <button
          className="refresh-button"
          onClick={onRefresh}
          disabled={refreshing}
        >
          <RefreshCw
            size={15}
            className={refreshing ? "refresh-spinning" : ""}
          />
          <span>{refreshing ? "Refreshing..." : "Refresh"}</span>
        </button>
      </div>
    </header>
  );
}

export default Topbar;
