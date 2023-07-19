import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getUserData, getUserSubmissions } from "../api/UserApi";
import { Button, Modal, Image, Col, Row, Stack } from 'react-bootstrap';
import { MdCheck, MdClose, MdQuestionMark, MdSettings } from "react-icons/md";
import { User } from "../types";
import Submission from "../types/Submission";
import useAuth from "../useAuth";

const Profile = () => {
    const { userId } = useParams();
    // const [user, setUser] = useState<User | null>(null);
    const [submissions, setSubmissions] = useState<Submission[][] | null>(null);
    const [selectedSubmission, setSelectedSubmission] = useState<Submission | null>(null);
    const [stats, setStats] = useState<{ bikeKm: number, walkKm: number } | null>(null);
    const { user } = useAuth();


    const id = userId === undefined ? "" : userId;


    useEffect(() => {
        // getUserData(id).then(setUser); //userId je null
        getUserSubmissions(1).then((subs) => {
            let splitted: Submission[][] = [];
            let tree: Submission[] = [] // yes strom

            let bikeKm = 0;
            let walkKm = 0;

            subs.forEach((sub: Submission, index: number) => {
                tree.push(sub);
                if (tree.length == 3) {
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

    const renderIcon = () => {
        if (!selectedSubmission!.reviewed) {
            return <MdQuestionMark />;
        }
        return selectedSubmission!.accepted ? <MdCheck /> : <MdClose />;
    }


    return (
        <div style={{ display: 'flex', alignContent: 'center', justifyContent: 'center' }}>
            <div style={{ width: '70%' }}>
                {user &&
                    <>
                        <Row style={{ display: 'flex', alignContent: 'center', justifyContent: 'center' }}>
                            {/* @ts-ignore */}
                            <span>{user.firstName} {user.lastName} <MdSettings style={{ marginLeft: '2%' }} /></span>
                        </Row >
                        <Row>
                            {/* @ts-ignore */}
                            <Col> <span>{user.faculty.name}</span></Col>
                            {/* @ts-ignore */}
                            <Col><span>{user.email} </span></Col>
                            {stats && <>
                                <Col><span>{stats.bikeKm} km</span></Col>
                                <Col><span>{stats.walkKm} km</span></Col>
                            </>}

                        </Row>
                        <hr className="hr-text" style={{ height: '1px' }} />
                    </>

                }

                {submissions?.map((subs: Submission[], index: number) => {
                    return (
                        <Stack key={index} direction='horizontal' gap={0} style={{ width: 'max-content' }}>
                            {subs.map(sub => (
                                <Button key={sub.id} variant="primary" onClick={() => handleSelectSubmission(sub.id)}>
                                    {new Date(sub.date).toDateString()}
                                </Button>
                            ))}
                        </Stack>

                    )
                })}

                {selectedSubmission &&
                    <Modal
                        show={selectedSubmission != null}
                        onHide={() => setSelectedSubmission(null)}
                        backdrop="static"
                        keyboard={false}
                    >

                        <Modal.Body style={{ padding: 0 }}>
                            <div className="container" style={{ padding: 0 }}>
                                <Row>
                                    <Col sm style={{ padding: 0 }}>
                                        <Image src={selectedSubmission.image} rounded />
                                    </Col>
                                    <Col sm> {/* FIXME */}
                                        <Row>
                                            <Col sm>
                                                <h5>{selectedSubmission.activity.name}</h5>
                                            </Col>
                                            <Col sm>
                                                <MdClose onClick={() => handleSelectSubmission(null)} />
                                            </Col>
                                        </Row>
                                        <p>Date: {new Date(selectedSubmission.date).toDateString()}</p>
                                        <p>Status: {renderIcon()}</p> {/* TODO trans */}
                                        <p>Distance: {selectedSubmission.distance} km</p>
                                        <p>Elevation: {selectedSubmission.elevation} m</p>
                                        {selectedSubmission.comment && <p>Comment: {selectedSubmission.comment}</p>}
                                    </Col>
                                </Row>
                            </div>
                        </Modal.Body>
                    </Modal>
                }
            </div >
        </div>
    )
}

export default Profile;
