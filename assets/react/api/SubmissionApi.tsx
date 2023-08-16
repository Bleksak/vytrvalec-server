import axios from "axios";
import UploadSubmissionData from "../types/formDataTypes/UploadSubmissionData";

export const uploadSubmission = async (data: UploadSubmissionData) => {
    return axios.postForm('/api/submission/create', data).then(
        res => {
            console.log('res', res);
            return res.data
        },
        err => console.log('err', err)
    );
}

//TODO
export const deleteSubmission = async (id: number) => {
    return axios.post('/api/submission/delete', id).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    )
}

