
export default function Product() {
  const router = useRouter();
  const { setHiddenData } = useAppContext(); // Use the custom hook

  const navigateToProductAbs = () => {

    router.push('/productdetail', undefined, { shallow: true });
  };

  return (
    <div style={{ padding: '20px' }}>
      <h1>Product Page</h1>
      <div className="product" onClick={navigateToProductAbs}>
        go to product abs
      </div>
    </div>
  );
}
