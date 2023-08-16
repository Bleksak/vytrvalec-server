import Activity from "./Activity";
import Season from "./Season";

interface Submission {
    id: number;
    accepted: boolean;
    season: Season;
    elevation: number;
    distance: number;
    reviewed: boolean;
    image: string;
    activity: Activity;
    date: Date;
    comment: string;
}

export default Submission;