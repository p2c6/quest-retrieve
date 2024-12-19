export type Role = {
    name: string;
}

export type StoreRole = Role;

export type UpdateRole = Role & {
    id: number;
}

export type GetRole = {
    id: number;

}

export type DeleteRole = GetRole;