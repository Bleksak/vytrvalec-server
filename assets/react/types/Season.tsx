import Charity from "./Charity";

interface Season {
    id: number | null;
    start: string | Date;
    end?: string | Date;
    charity: Charity;
}

export default Season;