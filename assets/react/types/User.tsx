import Faculty from "./Faculty";

interface User {
    id: number;
    firstName: string;
    lastName: string;
    userIdentifier: string;
    faculty: Faculty
    email: string;
    roles: string[];
    banned: boolean;

}
export default User;