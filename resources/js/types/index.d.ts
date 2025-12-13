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

export interface Exercise {
    id: number;
    name: string;
    equipment_type: string;
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
