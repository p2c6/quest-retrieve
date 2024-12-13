export type Category = {
    name: string;
}

export type StoreCategory = Category;

export type UpdateCategory = Category & {
    id: number;
}

export type DeleteCategory = {
    id: number;
}

export type GetCategory = DeleteCategory;