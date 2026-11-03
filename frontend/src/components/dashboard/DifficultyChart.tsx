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

  const visibleData = chartData.filter((item) => item.value > 0);

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
                data={visibleData}
                dataKey="value"
                nameKey="name"
                cx="50%"
                cy="50%"
                innerRadius={58}
                outerRadius={82}
                paddingAngle={3}
              >
                {visibleData.map((item) => (
                  <Cell
                    key={item.name}
                    fill={COLORS[item.name as keyof typeof COLORS]}
                  />
                ))}
              </Pie>

              <Tooltip
                formatter={(value, name) => [
                  `${value} (${((Number(value) / total) * 100).toFixed(1)}%)`,
                  name,
                ]}
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
                }}
                itemStyle={{
                  color: "#172033",
                  fontSize: "13px",
                  fontWeight: 600,
                }}
              />
            </PieChart>
          </ResponsiveContainer>

          <div className="difficulty-chart-legend">
            {chartData.map((item) => {
              const percentage = total > 0 ? (item.value / total) * 100 : 0;

              return (
                <div className="difficulty-legend-item" key={item.name}>
                  <span
                    className="difficulty-legend-dot"
                    style={{
                      backgroundColor: COLORS[item.name as keyof typeof COLORS],
                    }}
                  />

                  <span>{item.name}</span>

                  <strong>{item.value}</strong>

                  <span className="difficulty-legend-percentage">
                    {percentage.toFixed(1)}%
                  </span>
                </div>
              );
            })}
          </div>
        </div>
      )}
    </div>
  );
}

export default DifficultyChart;
