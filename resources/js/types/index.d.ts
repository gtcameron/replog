export interface Auth {
    user: User;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
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

export interface ActivityType {
    id: number;
    family_id: number;
    name: string;
    description: string | null;
    color: string;
    icon: string | null;
    activities_count?: number;
    created_at: string;
    updated_at: string;
}

export interface Activity {
    id: number;
    family_id: number;
    name: string;
    activity_type_id: number | null;
    activity_type?: ActivityType | null;
    equipment_type: string | null;
    muscle_group: string | null;
    description: string | null;
    instructions: string | null;
    created_at: string;
    updated_at: string;
}

export interface ActivityLog {
    id: number;
    family_id: number;
    activity_id: number;
    activity?: Activity;
    user_id: number;
    user?: User;
    logged_by_id: number;
    logged_by?: User;
    performed_at: string;
    sets: number | null;
    reps: number | null;
    weight: number | null;
    duration_seconds: number | null;
    distance: number | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

export interface EquipmentType {
    value: string;
    label: string;
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
