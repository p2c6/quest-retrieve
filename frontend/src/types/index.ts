export type UserLogin  = {
    email: string;
    password: string;
}

export type UserRegistration = {
    email: string;
    password: string;
    last_name: string;
    first_name: string;
    birthday: string;
    contact_no: string;
}

export type Profile =  {
    id: number;
    first_name: string;
    last_name: string;
    birthday: string;
    contact_no: string;
    avatar: string | null;
    user_id: number;
    created_at: string;
    updated_at: string | null;
    profile_date_updated: string | null;

}

export type User = {
    id: number;
    email: string;
    profile: Profile | null;
}