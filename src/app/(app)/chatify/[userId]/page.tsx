"use client"
import { useRouter, useParams, useSearchParams } from 'next/navigation'
// import Chatify from '././Chatify/page';
import Chatify from '../page';

export default function ChatifyPage({params}: any){
    const router = useRouter();// Use useNavigation instead of useRouter
    const searchParams = useSearchParams()
    const { userId } = params.userId; // Access query params using navigation.query
    return <Chatify userId={params.userId} />;
}