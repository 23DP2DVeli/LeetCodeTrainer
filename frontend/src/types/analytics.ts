export interface AnalyticsOverview {
  total_submissions: number;
  accepted_submissions: number;
  failed_submissions: number;
  solved_problems: number;
  success_rate: number;
}

export interface DifficultyBreakdown {
  Easy: number;
  Medium: number;
  Hard: number;
}

export interface DailyActivity {
  date: string;
  submissions: number;
  solved_problems: number;
}

export interface LanguageStats {
  total_submissions: number;
  accepted_submissions: number;
}

export interface AnalyticsResponse {
  overview: AnalyticsOverview;
  difficulty_breakdown: DifficultyBreakdown;
  daily_activity: DailyActivity[];
  language_breakdown: Record<string, LanguageStats>;
}
