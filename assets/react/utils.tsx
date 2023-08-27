import moment from "moment";
import Season from "./types/Season";

export const getNewSeason = (): Season => {
    return {
        id: null,
        start: moment().format(),
        charity: {
            id: null,
            name: '',
            description: '',
        }
    };
}