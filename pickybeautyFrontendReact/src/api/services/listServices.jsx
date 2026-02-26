
import { EXPRESS_BACKEND } from "../../../env";


export async function listServices() {
    const response = await fetch(`${EXPRESS_BACKEND}/api/v1/service`);
    if (!response.ok) {
        throw new Error('Failed to fetch services');
    }
    const data = await response.json();

    // console.log(data);
    
    return data;
}