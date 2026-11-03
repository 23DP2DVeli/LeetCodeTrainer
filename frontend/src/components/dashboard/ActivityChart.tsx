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

interface ActivityChartProps {
  data: DailyActivity[];
}

function ActivityChart({ data }: ActivityChartProps) {
  const chartData = data.map((item) => ({
    ...item,
    date: new Date(item.date).toLocaleDateString("en", {
      month: "short",
      day: "numeric",
    }),
  }));

  return (
    <div className="activity-chart">
      <div className="activity-chart-header">
        <div>
          <h3>Activity overview</h3>
          <p>Your submissions and solved problems over time</p>
        </div>
      </div>

      <ResponsiveContainer width="100%" height={280}>
        <LineChart
          data={chartData}
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

          <Tooltip />

          <Line
            type="monotone"
            dataKey="submissions"
            name="Submissions"
            stroke="#356ae6"
            strokeWidth={2.5}
            dot={false}
            activeDot={{ r: 5 }}
          />

          <Line
            type="monotone"
            dataKey="solved_problems"
            name="Solved"
            stroke="#16845b"
            strokeWidth={2.5}
            dot={false}
            activeDot={{ r: 5 }}
          />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}

export default ActivityChart;
