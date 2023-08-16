import axios from "axios";

export const getAllFaculties = () => {
    return axios.get('/api/faculty/list').then(
        res => {
            console.log('re', res);
            return res.data;
        },
        err => console.log('err', err)
    )
}