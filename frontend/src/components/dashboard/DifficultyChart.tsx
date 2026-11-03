import { Cell, Pie, PieChart, ResponsiveContainer, Tooltip } from "recharts";
import type { DifficultyBreakdown } from "../../types/analytics";
import "./DashboardCharts.css";

interface DifficultyChartProps {
  data: DifficultyBreakdown;
}

const COLORS = {
  Easy: "#16845b",
  Medium: "#d99a32",
  Hard: "#d14b4b",
};

function DifficultyChart({ data }: DifficultyChartProps) {
  const chartData = [
    { name: "Easy", value: data.Easy },
    { name: "Medium", value: data.Medium },
    { name: "Hard", value: data.Hard },
  ];

  const total = chartData.reduce((sum, item) => sum + item.value, 0);

  return (
    <div className="difficulty-chart">
      <div className="difficulty-chart-header">
        <div>
          <h3>Difficulty breakdown</h3>
          <p>Problems solved by difficulty</p>
        </div>
      </div>

      {total === 0 ? (
        <p className="difficulty-chart-empty">No solved problems yet.</p>
      ) : (
        <div className="difficulty-chart-content">
          <ResponsiveContainer width="100%" height={220}>
            <PieChart>
              <Pie
                data={chartData.filter((item) => item.value > 0)}
                dataKey="value"
                nameKey="name"
                cx="50%"
                cy="50%"
                innerRadius={58}
                outerRadius={82}
                paddingAngle={3}
              >
                {chartData
                  .filter((item) => item.value > 0)
                  .map((item) => (
                    <Cell
                      key={item.name}
                      fill={COLORS[item.name as keyof typeof COLORS]}
                    />
                  ))}
              </Pie>

              <Tooltip />
            </PieChart>
          </ResponsiveContainer>

          <div className="difficulty-chart-legend">
            {chartData.map((item) => (
              <div className="difficulty-legend-item" key={item.name}>
                <span
                  className="difficulty-legend-dot"
                  style={{
                    backgroundColor: COLORS[item.name as keyof typeof COLORS],
                  }}
                />
                <span>{item.name}</span>
                <strong>{item.value}</strong>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

export default DifficultyChart;
