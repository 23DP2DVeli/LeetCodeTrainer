import {
  CartesianGrid,
  Line,
  LineChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";

import type { DailyActivity } from "../../types/analytics";
import "./DashboardCharts.css";

interface PerformanceTrendProps {
  data: DailyActivity[];
}

function formatDate(date: string) {
  return new Date(`${date}T00:00:00`).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
  });
}

function PerformanceTrend({ data }: PerformanceTrendProps) {
  return (
    <div className="activity-chart">
      <div className="activity-chart-header">
        <div>
          <h3>Submission performance</h3>
          <p>Accepted submissions compared with total attempts</p>
        </div>
      </div>

      <ResponsiveContainer width="100%" height={280}>
        <LineChart
          data={data}
          margin={{ top: 16, right: 12, left: -16, bottom: 4 }}
        >
          <CartesianGrid
            stroke="#e5e9f0"
            strokeDasharray="3 3"
            vertical={false}
          />

          <XAxis
            dataKey="date"
            axisLine={false}
            tickLine={false}
            tick={{ fill: "#929bab", fontSize: 11 }}
            tickFormatter={formatDate}
            interval="preserveStartEnd"
            minTickGap={24}
            tickMargin={10}
          />

          <YAxis
            allowDecimals={false}
            axisLine={false}
            tickLine={false}
            tick={{ fill: "#929bab", fontSize: 12 }}
          />

          <Tooltip
            labelFormatter={(label) => formatDate(String(label))}
            contentStyle={{
              backgroundColor: "#ffffff",
              border: "1px solid #d1d5db",
              borderRadius: "0",
              padding: "10px 14px",
              boxShadow: "0 4px 12px rgba(0, 0, 0, 0.08)",
            }}
            labelStyle={{
              color: "#64748b",
              fontSize: "12px",
              marginBottom: "6px",
            }}
            itemStyle={{
              color: "#172033",
              fontSize: "13px",
              fontWeight: 600,
            }}
            cursor={{ stroke: "#94a3b8", strokeDasharray: "4 4" }}
          />

          <Line
            type="monotone"
            dataKey="submissions"
            name="Total submissions"
            stroke="#929bab"
            strokeWidth={2}
            dot={false}
            activeDot={{ r: 5 }}
          />

          <Line
            type="monotone"
            dataKey="accepted_submissions"
            name="Accepted submissions"
            stroke="#356ae6"
            strokeWidth={2.5}
            dot={false}
            activeDot={{ r: 5 }}
          />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}

export default PerformanceTrend;
