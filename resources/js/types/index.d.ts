export interface Auth {
    user: User;
}

export interface ActivitySet {
    id: number;
    workout_activity_id: number;
    set_number: number;
    reps: number | null;
    weight: number | null;
    duration_seconds: number | null;
    distance: number | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

export interface WorkoutActivity {
    id: number;
    family_id: number;
    workout_id: number | null;
    workout?: Workout;
    activity_id: number;
    activity?: Activity;
    user_id: number;
    user?: User;
    logged_by_id: number;
    logged_by?: User;
    performed_at: string;
    notes: string | null;
    sets?: ActivitySet[];
    created_at: string;
    updated_at: string;
}

export interface Workout {
    id: number;
    family_id: number;
    user_id: number;
    user?: User;
    started_by_id: number;
    started_by?: User;
    name: string | null;
    started_at: string;
    ended_at: string | null;
    notes: string | null;
    workout_activities?: WorkoutActivity[];
    workout_activities_count?: number;
    created_at: string;
    updated_at: string;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    activeWorkout: Workout | null;
};

export interface Family {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
}

export interface User {
    id: number;
    name: string;
    email: string | null;
    family_id: number | null;
    family?: Family | null;
    can_login: boolean;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface FamilyMember {
    id: number;
    name: string;
    email: string | null;
    can_login: boolean;
    created_at: string;
    updated_at: string;
}

export interface Activity {
    id: number;
    family_id: number;
    name: string;
    equipment_type: string | null;
    muscle_group: string | null;
    description: string | null;
    instructions: string | null;
    tracks_reps: boolean;
    tracks_weight: boolean;
    tracks_duration: boolean;
    tracks_distance: boolean;
    created_at: string;
    updated_at: string;
}

export interface EquipmentType {
    value: string;
    label: string;
}

export interface Invite {
    id: number;
    email: string;
    claimed_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

// Stats types
export interface MemberStats {
    member: FamilyMember;
    totalSessions: number;
    activitiesCount: number;
    lastActive: string | null;
}

export interface ProgressionPoint {
    date: string;
    value: number | null;
}

export interface ActivityProgression {
    activity: Activity;
    data: ProgressionPoint[];
    personalBest: number;
    lastValue: number;
    totalSessions: number;
}

export interface ActivityStats {
    activity: Activity;
    totalLogs: number;
    participantsCount: number;
    lastPerformed: string | null;
}

export interface LeaderboardEntry {
    member: FamilyMember;
    value: number;
    date: string;
    rank: number;
}

export interface ChartSeries {
    memberId: number;
    memberName: string;
    data: (number | null)[];
}

export interface ComparisonChartData {
    dates: string[];
    series: ChartSeries[];
}

// Workout activity history
export interface ActivityHistorySetEntry {
    set_number: number;
    reps: number | null;
    weight: number | null;
    duration_seconds: number | null;
    distance: number | null;
}

export interface ActivityHistoryEntry {
    id: number;
    performed_at: string;
    sets: ActivityHistorySetEntry[];
}

export interface ActivityHistoryData {
    recentLogs: ActivityHistoryEntry[];
    chartData: ProgressionPoint[];
    activity: Pick<Activity, 'id' | 'name' | 'tracks_reps' | 'tracks_weight' | 'tracks_duration' | 'tracks_distance'>;
}
