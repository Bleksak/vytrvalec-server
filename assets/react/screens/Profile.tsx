import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getUserData, getUserSubmissions } from "../api/UserApi";
import { Button, Modal, Image, Col, Row, Stack, Dropdown } from 'react-bootstrap';
import { MdCheck, MdClose, MdQuestionMark, MdSettings } from "react-icons/md";
import { User } from "../types";
import Submission from "../types/Submission";
import useAuth from "../useAuth";
import SubmissionModal from "../components/user/SubmissionModal";

const Profile = () => {
    const [submissions, setSubmissions] = useState<Submission[][] | null>(null);
    const [selectedSubmission, setSelectedSubmission] = useState<Submission | null>(null);
    const [stats, setStats] = useState<{ bikeKm: number, walkKm: number } | null>(null);
    const { user } = useAuth();


    useEffect(() => {
        getUserSubmissions(1).then((subs) => {
            let splitted: Submission[][] = [];
            let tree: Submission[] = [] // yes strom

            let bikeKm = 0;
            let walkKm = 0;

            subs.forEach((sub: Submission, index: number) => {
                tree.push(sub);
                if (tree.length == 4) {
                    splitted.push(tree);
                    tree = []
                } else if (index == subs.length - 1) {
                    splitted.push(tree);
                }

                if (sub.activity.name === 'Běh/Chůze') {
                    walkKm += sub.distance;
                } else {
                    bikeKm += sub.distance;
                }
            })

            setStats({ bikeKm: bikeKm, walkKm: walkKm });
            setSubmissions(splitted);
        });
    }, []);


    const handleSelectSubmission = (id: number | null) => {
        if (!id) setSelectedSubmission(null);
        let found = null;
        submissions?.forEach((subs: Submission[]) => {
            const f = subs.find(sub => sub.id === id);
            if (f) { found = f; return };
        });
        if (found) setSelectedSubmission(found);
    }




    return (
        <Row>
            <Col className="col-lg-3">
                {user &&
                    <>
                        <Row style={{ display: 'flex', alignContent: 'center', justifyContent: 'center' }}>
                            <strong>Under construction</strong>
                            {/* @ts-ignore */}
                            <span>{user.firstName} {user.lastName} <MdSettings style={{ marginLeft: '2%' }} /></span>
                        </Row >
                        <Row>
                            <strong>Faculty:</strong>
                            {/* @ts-ignore */}
                            <span>{user.faculty.name}</span>
                        </Row>
                        <Row>
                            <strong>Email:</strong>
                            {/* @ts-ignore */}
                            <span>{user.email} </span>
                        </Row>

                        <hr className="hr-text" style={{ height: '1px' }} />

                        {stats && <>
                            <span>{stats.bikeKm} km</span>
                            <span>{stats.walkKm} km</span>
                        </>}

                    </>

                }
            </Col>

            <Col >
                {submissions?.map((subs: Submission[], index: number) => {
                    return (
                        <Row key={index} style={{ margin: '1%', justifyContent: 'center' }}>
                            {subs.map(sub => (
                                <Image key={sub.id} src={sub.image} className="img" rounded onClick={() => handleSelectSubmission(sub.id)} />
                            ))}
                        </Row>

                    )
                })}
            </Col>

            {selectedSubmission &&
                <SubmissionModal submission={selectedSubmission} onClose={() => setSelectedSubmission(null)} />
            }

        </Row >
    )
}

export default Profile;
