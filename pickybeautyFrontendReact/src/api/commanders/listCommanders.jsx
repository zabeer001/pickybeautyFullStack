import { EXPRESS_BACKEND } from "../../../env";



export async function listCommanders(page) {
    const response = await fetch(`${EXPRESS_BACKEND}/api/v1/commander?page=${page}`);
    if (!response.ok) {
        throw new Error('Failed to fetch commanders');
    }
    const data = await response.json();
    return data;
}