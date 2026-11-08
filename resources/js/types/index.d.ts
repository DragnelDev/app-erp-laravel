export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface CompanySummary {
    id: number;
    name: string;
}

export interface Company extends CompanySummary {
    base_currency: string;
    foreign_currency: string;
    exchange_rate: string;
}

export interface Branch {
    id: number;
    name: string;
    code: string;
}

export interface Tenancy {
    company: Company | null;
    companies: CompanySummary[];
    branches: Branch[];
    all_branches: boolean;
}

export interface Flash {
    success?: string | null;
    error?: string | null;
    info?: string | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
        permissions: string[];
    };
    tenancy: Tenancy;
    flash: Flash;
};
