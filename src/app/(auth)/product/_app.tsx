import { AppWrapper } from '../context';

function MyApp({ Component, pageProps }) {
  console.log("7878");
  return (
    <AppWrapper>
      <Component {...pageProps} />
    </AppWrapper>
  );
}

export default MyApp;
