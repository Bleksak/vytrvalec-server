
import { registerReactControllerComponents } from '@symfony/ux-react';

// any CSS you import will output into a single css file (app.css in this case)

// start the Stimulus application
registerReactControllerComponents(require.context('./react', true, /\.tsx?$/))
import './bootstrap';

import './styles/global.scss';
import './styles/main.scss';
