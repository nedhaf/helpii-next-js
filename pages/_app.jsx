import React from 'react';
import MyContextProvider from './context';

function MyApp({ Component, pageProps }) {
  return (
    <MyContextProvider>
      <Component {...pageProps} />
    </MyContextProvider>
  );
}

export default MyApp;