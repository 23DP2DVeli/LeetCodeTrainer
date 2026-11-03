import type { AnalyticsResponse } from "../types/analytics";

const API_URL =
  import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api";

export async function getAnalytics(
  username: string,
): Promise<AnalyticsResponse> {
  const response = await fetch(
    `${API_URL}/users/${encodeURIComponent(username)}/analytics`,
    {
      headers: {
        Accept: "application/json",
      },
    },
  );

  if (!response.ok) {
    throw new Error(
      `Failed to fetch analytics (${response.status} ${response.statusText})`,
    );
  }

  return (await response.json()) as AnalyticsResponse;
}
