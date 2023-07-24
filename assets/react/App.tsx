import { BrowserRouter, } from 'react-router-dom'
import Navbar from './components/navbar/Navbar'
import React from "react";
//@ts-ignore
import _ from './i8n'
import Navigation from './Navigation'
import { AuthProvider } from './useAuth';
import Footer from './components/Footer';
import CustomNavbar from "./components/navbar/Navbar";

const App = (props: any) => {
    return (
        <BrowserRouter>
            <AuthProvider>
                <div > {/*className="container" */}
                    <CustomNavbar />
                    <Navigation {...props} />
                </div>
                <Footer />
            </AuthProvider>
        </BrowserRouter >
    );
}

export default App;
