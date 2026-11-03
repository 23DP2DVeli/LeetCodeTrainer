import { ActivityCalendar } from "react-activity-calendar";
import type { DailyActivity } from "../../types/analytics";
import "./DashboardCharts.css";

interface ActivityHeatmapProps {
  data: DailyActivity[];
}

function ActivityHeatmap({ data }: ActivityHeatmapProps) {
  const calendarData = data.map((day) => ({
    date: day.date,
    count: day.submissions,
    level:
      day.submissions === 0
        ? 0
        : day.submissions === 1
          ? 1
          : day.submissions <= 3
            ? 2
            : day.submissions <= 5
              ? 3
              : 4,
  }));

  const totalSubmissions = data.reduce((sum, day) => sum + day.submissions, 0);

  const activeDays = data.filter((day) => day.submissions > 0).length;

  return (
    <section className="activity-heatmap">
      <div className="activity-chart-header">
        <div>
          <h3>Activity overview</h3>
          <p>Your LeetCode activity over the last 30 days</p>
        </div>
      </div>

      <div className="heatmap-summary">
        <div>
          <strong>{totalSubmissions}</strong>
          <span> submissions</span>
        </div>

        <div>
          <strong>{activeDays}</strong>
          <span> active days</span>
        </div>
      </div>

      <ActivityCalendar
        className="leetcode-activity-calendar"
        data={calendarData}
        theme={{
          light: ["#edf0f4", "#c6d8fc", "#8fb4f8", "#568be9", "#2858c7"],
        }}
        colorScheme="light"
        blockSize={12}
        blockMargin={4}
        fontSize={11}
        showWeekdayLabels
        showMonthLabels
        showTotalCount={false}
        labels={{
          totalCount: "{{count}} submissions in the last 30 days",
        }}
        tooltips={{
          activity: {
            text: (activity) =>
              `${activity.count} submissions on ${activity.date}`,
            placement: "top",
            offset: 8,
            hoverRestMs: 0,
          },
        }}
      />
    </section>
  );
}

export default ActivityHeatmap;
