export interface Auth {
    user: User;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface ActivityType {
    id: number;
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

export interface EquipmentType {
    value: string;
    label: string;
}
